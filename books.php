<?php
session_start();
include "admin/db_conn.php";

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
        $category = $book['category_name'];
        $booksByCategory[$category][] = $book;
    }
} else {
    $noResults = true;
}

// Download trigger
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
            <a href="#categories.php">🏷️ Category</a>
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
                                <a href="book-details.php?id=<?= $book['id'] ?>">
                                    <img src="<?= $cover ?>" alt="<?= htmlspecialchars($book['title']) ?> Cover">
                                </a>
                                <div class="hover-buttons">
                                    <a href="read.php?id=<?= $book['id'] ?>">Read</a>
                                    <a href="?id=<?= $book['id'] ?>">Download</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php elseif (isset($noResults)): ?>
            <p style="text-align:center; margin-top: 2rem;">No books found for
                "<strong><?= htmlspecialchars($search) ?></strong>".</p>
        <?php else: ?>
            <p>No books available.</p>
        <?php endif; ?>
    </section>

    <script>
        const hamburger = document.getElementById('hamburger');
        const drawer = document.getElementById('drawer');

        hamburger.addEventListener('click', () => {
            drawer.classList.toggle('open');
        });

        window.addEventListener('click', (event) => {
            if (!drawer.contains(event.target) && event.target !== hamburger) {
                drawer.classList.remove('open');
            }
        });

        document.querySelectorAll('#drawer a').forEach(link => {
            link.addEventListener('click', () => {
                drawer.classList.remove('open');
            });
        });
    </script>
</body>

</html>