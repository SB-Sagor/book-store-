<?php
session_start();
include "admin/db_conn.php";

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in!");
}

function isValidISBN($isbn)
{
    $isbn = str_replace(['-', ' '], '', $isbn);
    return preg_match('/^(?:\d{9}X|\d{10}|\d{13})$/', $isbn);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $book_title = trim($_POST['book_title']);
    $request_details = trim($_POST['request_details']);
    $combined = isset($_POST['combined_isbn']) ? trim($_POST['combined_isbn']) : '';

    $isbn = '';
    $isbn_raw = '';

    if (!empty($combined)) {
        if (strpos($combined, ',') !== false) {
            list($isbn, $isbn_raw) = array_map('trim', explode(',', $combined, 2));
        } else {
            $isbn = $combined;
            $isbn_raw = $combined;
        }

        if (!isValidISBN($isbn)) {
            $message = " Invalid ISBN format. Expected ISBN-10 (e.g. 0198526636) or ISBN-13 (e.g. 9783161484100).";
        }
    }

    if (empty($message)) {
        $stmt = $conn->prepare("INSERT INTO book_requests (user_id, book_title, isbn, isbn_raw, request_details) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $book_title, $isbn, $isbn_raw, $request_details);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: index.php?success=" . urlencode("Request submitted successfully!"));
            exit();
        } else {
            $message = " Error: " . $stmt->error;
            $stmt->close();
            $conn->close();
        }
    } else {
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Request a Book</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css?v=1.1">
</head>

<body>
    <div class="form-container">
        <h2>Request a Book</h2>

        <?php if (!empty($message)) : ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="book_title" placeholder="Book Title" required>
            <input type="text" name="combined_isbn" placeholder="ISBN / Raw ISBN (optional)">
            <textarea name="request_details" placeholder="Why do you need this book?" rows="5" required></textarea>
            <button type="submit">Submit Request</button>
        </form>
    </div>
</body>

</html>