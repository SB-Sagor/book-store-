<?php
session_start();

// If read.php is in project root (book-store-/)
// and db_conn.php is inside admin/, use:
include "admin/db_conn.php";

// Validate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid request!");
}

$id = (int) $_GET['id'];

// Fetch book file path
$sql = "SELECT title, file FROM books WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $book = $result->fetch_assoc();
        $title = $book['title'];
        $filePath = $book['file'];
    } else {
        die("Book not found!");
    }
    $stmt->close();
} else {
    die("Query prepare failed!");
}

$conn->close();

// Check if file exists
if (empty($filePath) || !file_exists($filePath)) {
    die("PDF file not available!");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title); ?> - Read PDF</title>
    <style>
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
    }

    .navbar {
        background-color: #555;
        /* gray color */
        color: white;
        padding: 12px 20px;
        font-size: 18px;
    }

    iframe {
        width: 100%;
        height: calc(100vh - 50px);
        /* subtract navbar height */
        border: none;
    }
    </style>
</head>

<body>
    <!-- Gray Navbar -->
    <div class="navbar">
        📚 Open Book — Reading: <?= htmlspecialchars($title); ?>
    </div>

    <!-- Show PDF inside iframe -->
    <iframe src="<?= htmlspecialchars($filePath); ?>"></iframe>
</body>

</html>