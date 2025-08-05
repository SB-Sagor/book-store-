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
    <link rel="stylesheet" href="books.css?v=1.0">
    <style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background-color: #f4f4f4;
        margin: 0;
    }

    .details-container {
        max-width: 900px;
        margin: 40px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        display: flex;
        gap: 30px;
        flex-wrap: wrap;
    }

    .details-container img {
        width: 220px;
        border-radius: 12px;
        object-fit: cover;
    }

    .details-info {
        flex: 1;
        min-width: 250px;
    }

    .details-info h2 {
        margin-top: 0;
        font-size: 28px;
        color: #333;
    }

    .details-info p {
        margin: 10px 0;
        color: #555;
        font-size: 16px;
        line-height: 1.5;
    }

    .download-btn {
        margin-top: 20px;
        display: inline-block;
        padding: 10px 20px;
        background-color: crimson;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-weight: bold;
        transition: background-color 0.3s ease;
    }

    .download-btn:hover {
        background-color: rgb(181, 5, 40);
    }
    </style>
</head>

<body>
    <div class="details-container">
        <img src="<?= htmlspecialchars($book['cover']) ?>" alt="<?= htmlspecialchars($book['title']) ?> Cover">

        <div class="details-info">
            <h2><?= htmlspecialchars($book['title']) ?></h2>
            <p><strong>Author:</strong> <?= htmlspecialchars($book['author_name']) ?></p>
            <p><strong>Category:</strong> <?= htmlspecialchars($book['category_name']) ?></p>
            <p><strong>ISBN:</strong> <?= htmlspecialchars($book['isbn']) ?></p>
            <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($book['description'])) ?></p>

            <?php if (!empty($book['file'])): ?>
            <a href="<?= htmlspecialchars($book['file']) ?>" class="download-btn" download>📥 Download Book</a>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>