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
            // 🔍 Check if user has at least 5 coins
            $coin_check = $conn->prepare("SELECT coins FROM users WHERE id = ?");
            $coin_check->bind_param("i", $user_id);
            $coin_check->execute();
            $coin_check->bind_result($coins);
            $coin_check->fetch();
            $coin_check->close();

            if ($coins < 5) {
                $conn->close();
                $message = "You need at least 5 coins to submit a book request.";
            }

            // Deduct 5 coins from user
            $deduct_stmt = $conn->prepare("UPDATE users SET coins = GREATEST(coins - 5, 0) WHERE id = ?");
            $deduct_stmt->bind_param("i", $user_id);
            $deduct_stmt->execute();
            $deduct_stmt->close();

            $conn->close();
            header("Location: index.php?success=" . urlencode("Request submitted successfully! 5 coins deducted."));
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
        <?php
        $user_id = $_SESSION['user_id'];
        $coin_stmt = $conn->prepare("SELECT coins FROM users WHERE id = ?");
        $coin_stmt->bind_param("i", $user_id);
        $coin_stmt->execute();
        $coin_stmt->bind_result($user_coins);
        $coin_stmt->fetch();
        $coin_stmt->close();
        ?>
        <p style="margin-bottom: 10px;">💰 Your Coins: <?= htmlspecialchars($user_coins) ?></p>

        <form method="POST">
            <input type="text" name="book_title" placeholder="Book Title" required>
            <input type="text" name="combined_isbn" placeholder="ISBN / Raw ISBN (optional)">
            <textarea name="request_details" placeholder="Why do you need this book?" rows="5" required></textarea>
            <?php if ($user_coins < 5): ?>
                <p style="color: red; margin-top: 5px;">⚠️ Need at least 5 coins to submit a request.</p>
                <button type="submit" disabled style="background-color: #ccc; cursor: not-allowed;">Submit Request</button>
            <?php else: ?>
                <button type="submit">Submit Request</button>
            <?php endif; ?>
        </form>

    </div>
</body>

</html>