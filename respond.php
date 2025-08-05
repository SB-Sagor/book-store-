<?php
session_start();
include "admin/add-books.php";

if (!isset($_SESSION['user_id'])) {
    die("Please login first!");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request_id = $_POST['request_id'];
    $user_id = $_SESSION['user_id'];
    $response_message = $_POST['response_message'];

    // File upload logic
    $target_dir = "uploads/";
    $file_name = basename($_FILES["book_file"]["name"]);
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["book_file"]["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO book_responses (request_id, user_id, response_message, book_file) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $request_id, $user_id, $response_message, $target_file);

        if ($stmt->execute()) {
            // Mark request as fulfilled
            $conn->query("UPDATE book_requests SET status='fulfilled' WHERE id=$request_id");
            echo "Book provided successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "File upload failed!";
    }
}

$request_id = $_GET['request_id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Provide Book</title>
</head>

<body>
    <h2>Provide Book</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="request_id" value="<?= $request_id ?>">
        <textarea name="response_message" placeholder="Message for requester" required></textarea><br>
        <input type="file" name="book_file" required><br>
        <button type="submit">Submit</button>
    </form>
</body>

</html>