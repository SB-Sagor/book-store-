<?php
include "admin/db_conn.php";

// Get search keyword
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Initialize an empty array for categorized books
$booksByCategory = [];

// Prepare query with optional search
if (!empty($search)) {
    $searchLike = '%' . $search . '%';
    $stmt = $conn->prepare(
        "SELECT books.*, authors.name AS author_name, category.name AS category_name
         FROM books
         JOIN authors ON books.author_id = authors.id
         JOIN category ON books.category_id = category.id
         WHERE books.title LIKE ? OR authors.name LIKE ? OR category.name LIKE ?
         ORDER BY category.name ASC, books.id DESC"
    );
    $stmt->bind_param("sss", $searchLike, $searchLike, $searchLike);
} else {
    $stmt = $conn->prepare(
        "SELECT books.*, authors.name AS author_name, category.name AS category_name
         FROM books
         JOIN authors ON books.author_id = authors.id
         JOIN category ON books.category_id = category.id
         ORDER BY category.name ASC, books.id DESC"
    );
}

// Execute and organize results
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

// Handle file download
if (isset($_GET['id'])) {
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
    <link rel="stylesheet" href="style.css">
</head>
<body>
<!-- Navigation Bar -->
<nav class="navbar">
    <div class="navtext">Open Book</div>
    <ul>
        <li><a href="books.php"><span>📚</span> All Books</a></li>
        <li><a href="index.php"><span>📨</span> Replies</a></li>
        <li><a href="#categories"><span>🏷️</span> Category</a></li>
        <li><a href="upload.php"><span>⏫</span> Upload</a></li>
        <li><a href="request.php"><span>💬</span> Request</a></li>
    </ul>
    <div class="hamburger" id="hamburger">&#9776;</div>
</nav>

<!-- Side Drawer -->
<div class="drawer" id="drawer">
    <ul>      
        <li><a href="books.php"><span>📚</span> All Books</a></li>
        <li><a href="login.php"><span>👤</span> Accounts</a></li>
        <li><a href="index.php"><span>📨</span> Replies</a></li>
        <li><a href="#categories"><span>🏷️</span> Category</a></li>
        <li><a href="upload.php"><span>⏫</span> Upload</a></li>
        <li><a href="request.php"><span>💬</span> Request</a></li>
    </ul>
</div>

<!-- Search Bar -->
<section>
    <div class="search-bar">
        <form method="GET">
            <input type="text" name="search" placeholder="Search for books, authors, categories..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
        </form>
    </div>
</section>

<!-- Book Display Section -->
<section>
    <?php if (!empty($booksByCategory)): ?>
        <?php foreach ($booksByCategory as $category => $books): ?>
            <div class="category-section">
                <h2><?= htmlspecialchars($category) ?></h2>
                <div class="book-container">
                    <?php foreach ($books as $book): ?>
                        <div class="book-card">
                           
                            <?php
                            $cover = !empty($book['cover']) ? "". htmlspecialchars($book['cover']) : "admin/uploads/covers/default-cover.jpg";
                            ?>
                            <img src="<?= $cover ?>" alt="<?= htmlspecialchars($book['title']) ?> Cover">
                           
                            <a href="?id=<?= $book['id'] ?>" class="download-btn">Download</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php elseif (isset($noResults)): ?>
        <p style="text-align:center; margin-top: 2rem;">No books found for "<strong><?= htmlspecialchars($search) ?></strong>".</p>
    <?php else: ?>
        <p>No books available.</p>
    <?php endif; ?>
</section>

<!-- JavaScript -->
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
