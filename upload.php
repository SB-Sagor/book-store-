<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in!");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['book_file'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $category = $_POST['category'];
    $uploaded_by = $_SESSION['user_id'];

    $target_dir = "uploads/";
    $file_name = basename($_FILES["book_file"]["name"]);
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["book_file"]["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO books (title, author, category, file_path, uploaded_by) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $title, $author, $category, $target_file, $uploaded_by);
        if ($stmt->execute()) {
            echo "Book uploaded successfully!";
            header("Location:index.php");
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "File upload failed!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload a Book</title>
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
            padding: 60px ;
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
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0px;
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
    <h2>Upload a Book</h2>

    <form method="POST" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="Book Title" required>
    <input type="text" name="author" placeholder="Author" required>
    <input type="text" name="category" placeholder="Category" required>
    <input type="file" name="book_file" required>
    <button type="submit">Upload Book</button>
</form>
    </div>

</body>
</html>
 

