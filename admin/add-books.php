<?php
session_start();
include "db_conn.php";

// Fetch authors
$authorQuery = "SELECT * FROM authors";
$authorResult = $conn->query($authorQuery);

// Fetch categories
$categoryQuery = "SELECT * FROM category";
$categoryResult = $conn->query($categoryQuery);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = htmlspecialchars($_POST["book_title"]);
    $description = htmlspecialchars($_POST["book_description"]);
    $author_id = intval($_POST["book_author"]);
    $category_id = intval($_POST["book_category"]);

    // File upload handling
    if (isset($_FILES["book_cover"]) && isset($_FILES["file"])) {
        $cover_name = $_FILES["book_cover"]["name"];
        $cover_tmp = $_FILES["book_cover"]["tmp_name"];
        $cover_ext = strtolower(pathinfo($cover_name, PATHINFO_EXTENSION));
        
        $file_name = $_FILES["file"]["name"];
        $file_tmp = $_FILES["file"]["tmp_name"];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_extensions = ["jpg", "jpeg", "png", "pdf"];

        if (in_array($cover_ext, ["jpg", "jpeg", "png"]) && in_array($file_ext, ["pdf"])) {
            $cover_path = "uploads/covers/" . uniqid() . "." . $cover_ext;
            move_uploaded_file($cover_tmp, $cover_path);

            $file_path = "uploads/books/" . uniqid() . "." . $file_ext;
            move_uploaded_file($file_tmp, $file_path);

            // Insert into database
            $sql = "INSERT INTO books (title, description, author_id, category_id, cover, file) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssiiss", $title, $description, $author_id, $category_id, $cover_path, $file_path);

            if ($stmt->execute()) {
                header("Location: admin.php?success=Book added successfully!");
                exit;
            } else {
                header("Location: admin.php?error=Error adding book!");
                exit;
            }
        } else {
            header("Location: admin.php?error=Invalid file type!");
            exit;
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .navbar {
            background-color: crimson;
            color: white;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navtext {
            cursor: pointer;

            font-size: 1rem;
            font-weight: bold;
        }

        .main-container {
            display: flex;
            flex: 1;
        }

        .admin {
            width: 20%;
            background-color: crimson;
            padding: 1rem;
            color: white;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .admin h3 {
            text-align: center;
        }

        .admin-list {
            list-style: none;
            padding: 0;
            margin: 0;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .admin-list li {
            cursor: pointer;
            padding: 10px;
            background-color: rgb(200, 10, 50);
            color: white;
            text-align: center;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .admin-list li:hover {
            background-color: rgb(190, 8, 44);
        }

        .content {
            flex: 1;
            padding: 2rem;
            background-color: crimson;
            border-radius: 5px;
            margin: 2px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .content h1 {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .mb-3 {
            margin-bottom: 1rem;

        }

        .mb-3 label {
            display: block;
            font-weight: bold;
        }

        .mb-3 input,
        .mb-3 select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: border 0.3s ease;
        }

        .mb-3 input:focus,
        .mb-3 select:focus {
            border-color: orangered;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: black;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        button:hover {
            background-color: #292323;

        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="navtext">Open Book</div>

    </nav>

    <div class="main-container">
        <section class="admin">
            <h3 id="backadmin">ADMIN PANEL</h3>
            <ul class="admin-list">
                <li id="allBooks">View Books</li>
                <li id="addAuthor">Add Author</li>
                <li id="addBooks">Add Books</li>
                <li id="addCategory">Add Category</li>
                <li id="requests">Book Requests</li>
                <li id="manageUsers">Manage Users</li>
                <li id="settings">Settings</li>
                <li id="logout">Logout</li>
            </ul>
        </section>

        <section class="content">
            <h1>Add New Book</h1>
            <form action="#" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="book_title">Book Title</label>
                    <input type="text" id="book_title" name="book_title">
                </div>
                <div class="mb-3">
                    <label for="book_description">Book Description</label>
                    <input type="text" id="book_description" name="book_description">
                </div>
                <div class="mb-3">
                    <label for="book_author">Book Author</label>
                    <select id="book_author" name="book_author" required>
                        <option value="0">Select author</option>
                        <?php while ($row = $authorResult->fetch_assoc()) : ?>
                            <option value="<?= $row['id'] ?>">
                                <?= htmlspecialchars($row['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="book_category">Book Category</label>
                    <select id="book_category" name="book_category">
                        <option value="0">Select category</option>
                        <?php while ($row = $categoryResult->fetch_assoc()) : ?>
                            <option value="<?= htmlspecialchars($row['id']) ?>">
                                <?= htmlspecialchars($row['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="book_cover">Book Cover</label>
                    <input type="file" id="book_cover" name="book_cover">
                </div>
                <div class="mb-3">
                    <label for="file">File</label>
                    <input type="file" id="file" name="file">
                </div>
                <button type="submit">Add Book</button>
            </form>
        </section>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelector(".navtext").addEventListener("click", () => {
                navigateTo("books.php");
            });
            document.getElementById("allBooks").addEventListener("click", () => navigateTo("books.php"));
            document.getElementById("backadmin").addEventListener("click", () => navigateTo("admin.php"));
            document.getElementById("addAuthor").addEventListener("click", () => navigateTo("add-author.php"));
            document.getElementById("addBooks").addEventListener("click", () => navigateTo("add-books.php"));
            document.getElementById("addCategory").addEventListener("click", () => navigateTo("add-category.php"));
            document.getElementById("manageUsers").addEventListener("click", () => navigateTo("manage-users.php"));
            document.getElementById("settings").addEventListener("click", () => navigateTo("settings.php"));
            document.getElementById("logout").addEventListener("click", () => navigateTo("logout.php"));
        });

        function navigateTo(page) {
            window.location.href = page;
        }
    </script>
</body>

</html>