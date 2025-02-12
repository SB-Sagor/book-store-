<?php
session_start();
include "db.php";
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in!");
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['user_id'])) {
        die("Please login first!");
    }

    $user_id = $_SESSION['user_id'];
    $book_title = $_POST['book_title'];
    $request_details = $_POST['request_details'];

    $stmt = $conn->prepare("INSERT INTO book_requests (user_id, book_title, request_details) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $book_title, $request_details);

    if ($stmt->execute()) {
        echo "Request submitted!";
        header("Location:index.php");
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Request a Book</title>
    <link rel="stylesheet" href="style.css">
    <!-- <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            /* display: flex; */
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 0px 25rem;

        }
        
        .form-container {
            background: white;
            padding: 60px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }
        
        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 10px;
            background: crimson;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: rgb(255, 145, 0);
        }
    </style> -->
</head>

<body>
    <div class="form-container">
        <h2>Request a Book</h2>
        <form method="POST">
            <input type="text" name="book_title" placeholder="Book Title" required><br>
            <textarea name="request_details" placeholder="Why do you need this book?" required></textarea><br>
            <button type="submit">Submit Request</button>
        </form>

    </div>
</body>

</html>