<?php
include "admin/db_conn.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid book ID.");
}

$id = intval($_GET['id']);
$stmt = $conn->prepare(
    "SELECT books.*, authors.name AS author_name, category.name AS category_name
     FROM books
     JOIN authors ON books.author_id = authors.id
     JOIN category ON books.category_id = category.id
     WHERE books.id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Book not found.");
}

$book = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($book['title']) ?> | Book Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="book-details.css?v=1.0">
</head>

<body>
    <nav class="navbar">
        <div class="navtext"><a href="index.php">Open Book</a></div>
        <ul>
            <li><a href="books.php"><span>📚</span> All Books</a></li>
            <li><a href="index.php"><span>📨</span> Replies</a></li>
            <li><a href="#categories"><span>🏷️</span> Category</a></li>
            <li><a href="upload.php"><span>⏫</span> Upload</a></li>
            <li><a href="request.php"><span>💬</span> Request</a></li>
            <li><a href="logout.php">🔓 Logout</a></li>
        </ul>
        <div class="hamburger" id="hamburger">&#9776;</div>
    </nav>

    <div class="drawer" id="drawer">
        <ul>
            <li><a href="books.php"><span>📚</span> All Books</a></li>
            <li><a href="login.php"><span>👤</span> Accounts</a></li>
            <li><a href="index.php"><span>📨</span> Replies</a></li>
            <li><a href="#categories"><span>🏷️</span> Category</a></li>
            <li><a href="upload.php"><span>⏫</span> Upload</a></li>
            <li><a href="request.php"><span>💬</span> Request</a></li>
            <li><a href="logout.php">🔓 Logout</a></li>
        </ul>
    </div>
    <div class="details-container">
        <img src="<?= htmlspecialchars($book['cover']) ?>" alt="<?= htmlspecialchars($book['title']) ?> Cover">

        <div class="details-info">
            <h2><?= htmlspecialchars($book['title']) ?></h2>
            <p><strong>Author:</strong> <?= htmlspecialchars($book['author_name']) ?></p>
            <p><strong>Category:</strong> <?= htmlspecialchars($book['category_name']) ?></p>
            <p><strong>ISBN:</strong> <?= htmlspecialchars($book['isbn']) ?></p>
            <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($book['description'])) ?></p>

            <?php if (!empty($book['file'])): ?>
                <a href="<?= htmlspecialchars($book['file']) ?>" class="download-btn" download>📥 Download Book</a> <a
                    href="read.php?id=<?= $book['id'] ?>" class="download-btn">📖 Read Books</a>

            <?php endif; ?>
        </div>
    </div>
</body>

</html>