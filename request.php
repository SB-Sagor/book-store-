<?php
session_start();
include "db.php";
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in!");
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $book_title = $_POST['book_title'];
    $request_details = $_POST['request_details'];

    $stmt = $conn->prepare("INSERT INTO book_requests (user_id, book_title, request_details) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $book_title, $request_details);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        $message = "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request a Book</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root {
            --primary: crimson;
            --hover: rgb(181, 5, 40);
            --background: #f4f4f4;
            --card: #ffffff;
            --text: #333;
            --radius: 10px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: var(--background);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background-color: var(--card);
            padding: 40px 30px;
            border-radius: var(--radius);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: var(--text);
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: var(--radius);
            resize: vertical;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: var(--primary);
            color: white;
            font-size: 16px;
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: var(--hover);
        }

        .message {
            margin-top: 10px;
            color: red;
            font-size: 14px;
        }

        @media (max-width: 480px) {
            .form-container {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Request a Book</h2>

        <?php if (!empty($message)) : ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="book_title" placeholder="Book Title" required>
            <textarea name="request_details" placeholder="Why do you need this book?" rows="5" required></textarea>
            <button type="submit">Submit Request</button>
        </form>
    </div>
</body>
</html>
