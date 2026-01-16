<?php
session_start();
include "admin/db_conn.php";
include "config.php"; // load API key

function getCachedApiBooks($filename, $query)
{
    $cacheFile = __DIR__ . "/cache/" . $filename;

    // If cache exists and is fresh (e.g., 24h)
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < 86400)) {
        return json_decode(file_get_contents($cacheFile), true);
    }

    // Otherwise fetch from API
    $data = fetchGoogleBooks($query);
    if ($data) {
        file_put_contents($cacheFile, json_encode($data));
    }
    return $data;
}

function fetchGoogleBooks($query)
{
    $url = "https://www.googleapis.com/books/v1/volumes?q=" . urlencode($query) . "&key=" . GOOGLE_BOOKS_API_KEY;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // useful for localhost testing
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200 && $response) {
        return json_decode($response, true);
    } else {
        return null; // gracefully handle errors
    }
}

$profile = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT name, email, avatar, coins FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $profile = $result->fetch_assoc();
    }
    $stmt->close();
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchClean = str_replace("-", "", $search);

$booksByCategory = [];
$noResults = false;
/* ---------------- PRELOAD API BOOKS ---------------- */
if (empty($search)) {
    $preloadCategories = ["programming", "science", "history"];

    foreach ($preloadCategories as $cat) {
        $googleData = getCachedApiBooks("preload_" . $cat . ".json", $cat);

        if (!empty($googleData['items'])) {
            foreach ($googleData['items'] as $item) {
                $volume = $item['volumeInfo'];

                $cover = "admin/uploads/covers/default-cover.jpg";
                if (!empty($volume['imageLinks']['thumbnail'])) {
                    $cover = $volume['imageLinks']['thumbnail'];
                } elseif (!empty($volume['imageLinks']['smallThumbnail'])) {
                    $cover = $volume['imageLinks']['smallThumbnail'];
                }

                $book = [
                    'id' => $item['id'],
                    'title' => $volume['title'] ?? 'Untitled',
                    'author_name' => implode(", ", $volume['authors'] ?? []),
                    'category_name' => $cat,
                    'cover' => $cover,
                    'isbn' => $volume['industryIdentifiers'][0]['identifier'] ?? '',
                    'source' => 'Google'
                ];

                $booksByCategory[$cat][] = $book;
            }
        }
    }
}


/* ---------------- LOCAL DB SEARCH ---------------- */
if (!empty($search)) {
    $searchLike = '%' . $search . '%';
    $searchISBN = '%' . $searchClean . '%';

    $stmt = $conn->prepare(
        "SELECT books.*, authors.name AS author_name, category.name AS category_name
         FROM books
         JOIN authors ON books.author_id = authors.id
         JOIN category ON books.category_id = category.id
         WHERE books.title LIKE ? OR authors.name LIKE ? OR category.name LIKE ? OR books.isbn LIKE ?
         ORDER BY category.name ASC, books.id DESC"
    );
    $stmt->bind_param("ssss", $searchLike, $searchLike, $searchLike, $searchISBN);
} else {
    $stmt = $conn->prepare(
        "SELECT books.*, authors.name AS author_name, category.name AS category_name
         FROM books
         JOIN authors ON books.author_id = authors.id
         JOIN category ON books.category_id = category.id
         ORDER BY category.name ASC, books.id DESC"
    );
}

$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    while ($book = $result->fetch_assoc()) {
        $book['source'] = 'Local';
        $category = $book['category_name'];
        $booksByCategory[$category][] = $book;
    }
}
/* ---------------- GOOGLE BOOKS API SEARCH ---------------- */
if (!empty($search)) {
    $googleData = fetchGoogleBooks($search); // using the curl helper

    if ($googleData === null) {
        echo "<p style='color:red;text-align:center;'>Google Books API request failed. Check your API key or restrictions.</p>";
    } elseif (!empty($googleData['items'])) {
        foreach ($googleData['items'] as $item) {
            $volume = $item['volumeInfo'];

            $book = [
                'id' => $item['id'],
                'title' => $volume['title'] ?? 'Untitled',
                'author_name' => implode(", ", $volume['authors'] ?? []),
                'category_name' => implode(", ", $volume['categories'] ?? ['Google Books']),
                'cover' => $volume['imageLinks']['thumbnail'] ?? "admin/uploads/covers/default-cover.jpg",
                'isbn' => $volume['industryIdentifiers'][0]['identifier'] ?? '',
                'source' => 'Google'
            ];

            $category = $book['category_name'];
            $booksByCategory[$category][] = $book;
        }
    }
}


/* ---------------- DOWNLOAD TRIGGER ---------------- */
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $baseDir = 'admin/uploads/books/';
    $result = $conn->query("SELECT file FROM books WHERE id = $id");

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $filePath = $baseDir . basename($row['file']);

        if (file_exists($filePath)) {
            header('Content-Type: ' . mime_content_type($filePath));
            header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            exit;
        } else {
            echo "Error: File not found.";
        }
    } else {
        echo "Error: Book not found.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Open Book</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="books.css">
    <link rel="stylesheet" href="avatars.css">
    <link rel="stylesheet" href="index.css">
</head>

<body>
    <div class="navbar">
        <button class="menu-toggle">&#9776;</button>
        <div class="sidebar" id="sidebar">
            <a href="index.php">📨 Replies</a>
            <a href="books.php">📚 All-Books</a>
            <a href="upload.php">⏫ Upload</a>
            <a href="request.php">💬 Request</a>
            <a href="logout.php">🔓 Logout</a>
        </div>

        <?php if ($profile): ?>
            <div class="avatar-wrapper" onclick="toggleProfileDropdown(event)">
                <img src="admin/uploads/avatars/<?= htmlspecialchars($profile['avatar'] ?: 'default-avatar.png') ?>"
                    alt="<?= htmlspecialchars($profile['name']) ?>'s Avatar" class="avatar-icon-small">
                <div class="profile-dropdown" id="profileDropdown">
                    <h4><?= htmlspecialchars($profile['name']) ?></h4>
                    <p><?= htmlspecialchars($profile['email']) ?></p>
                    <p title="Your current coin balance">💰 Coins: <?= htmlspecialchars($profile['coins']) ?></p>
                    <hr>
                </div>
            </div>
        <?php endif; ?>

        <div class="search-container">
            <form method="GET">
                <input type="text" placeholder="Search by isbn, author, or title..." name="search"
                    value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="download-btn">Search</button>
            </form>
        </div>
    </div>

    <section>
        <?php if (!empty($booksByCategory)): ?>
            <?php foreach ($booksByCategory as $category => $books): ?>
                <div class="category-section">
                    <h2><?= htmlspecialchars($category) ?></h2>
                    <div class="book-container">
                        <?php foreach ($books as $book): ?>
                            <div class="book-card">
                                <?php
                                $cover = !empty($book['cover']) ? htmlspecialchars($book['cover']) : "admin/uploads/covers/default-cover.jpg";
                                ?>
                                <?php if ($book['source'] === 'Google'): ?>
                                    <a href="https://books.google.com/books?id=<?= $book['id'] ?>">
                                        <img src="<?= $cover ?>" alt="<?= htmlspecialchars($book['title']) ?> Cover">
                                    </a>
                                    <div class="hover-buttons">
                                        <a href="https://books.google.com/books?id=<?= $book['id'] ?>">View</a>
                                    </div>
                                <?php else: ?>
                                    <a href="book-details.php?id=<?= $book['id'] ?>">
                                        <img src="<?= $cover ?>" alt="<?= htmlspecialchars($book['title']) ?> Cover">
                                    </a>
                                    <div class="hover-buttons">
                                        <a href="read.php?id=<?= $book['id'] ?>">Read</a>
                                        <a href="?id=<?= $book['id'] ?>">Download</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php elseif ($noResults): ?>
            <p style="text-align:center; margin-top: 2rem;">No books found for
                "<strong><?= htmlspecialchars($search) ?></strong>".</p>
        <?php else: ?>
            <p>No books available.</p>
        <?php endif; ?>
    </section>
</body>

</html>