<?php
session_start();
include "admin/db_conn.php";

function validate_and_create_dir($path)
{
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
}

function validate_uploaded_file($file, $allowed_types, $max_size)
{
    $file_ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    if (!in_array($file_ext, $allowed_types)) {
        return "Invalid file type! Only " . implode(", ", $allowed_types) . " allowed.";
    }
    if ($file["size"] > $max_size) {
        return "File size exceeds the limit of " . ($max_size / 1024 / 1024) . " MB.";
    }
    return null;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = htmlspecialchars($_POST["book_title"]);
    $description = htmlspecialchars($_POST["book_description"]);
    $author_name = htmlspecialchars($_POST["book_author"]);
    $category_name = htmlspecialchars($_POST["book_category"]);

    if (isset($_FILES["book_cover"]) && isset($_FILES["file"])) {
        $cover_error = validate_uploaded_file($_FILES["book_cover"], ["jpg", "jpeg", "png"], 2 * 1024 * 1024); // 2MB limit
        $file_error = validate_uploaded_file($_FILES["file"], ["pdf"], 5 * 1024 * 1024); // 5MB limit

        if ($cover_error || $file_error) {
            header("Location: add-books.php?error=" . htmlspecialchars($cover_error ?: $file_error));
            exit;
        }

        $cover_path = "admin/uploads/covers/" . uniqid() . "_" . basename($_FILES["book_cover"]["name"]);
        $file_path = "admin/uploads/books/" . uniqid() . "_" . basename($_FILES["file"]["name"]);

        validate_and_create_dir("admin/uploads/covers/");
        validate_and_create_dir("admin/uploads/books/");

        if (
            move_uploaded_file($_FILES["book_cover"]["tmp_name"], $cover_path) &&
            move_uploaded_file($_FILES["file"]["tmp_name"], $file_path)
        ) {

            // Check if author exists or insert new one
            $stmt = $conn->prepare("SELECT id FROM authors WHERE name = ?");
            $stmt->bind_param("s", $author_name);
            $stmt->execute();
            $stmt->bind_result($author_id);
            $stmt->fetch();
            $stmt->close();

            if (!$author_id) {
                $stmt = $conn->prepare("INSERT INTO authors (name) VALUES (?)");
                $stmt->bind_param("s", $author_name);
                $stmt->execute();
                $author_id = $stmt->insert_id;
                $stmt->close();
            }

            // Check if category exists or insert new one
            $stmt = $conn->prepare("SELECT id FROM category WHERE name = ?");
            $stmt->bind_param("s", $category_name);
            $stmt->execute();
            $stmt->bind_result($category_id);
            $stmt->fetch();
            $stmt->close();

            if (!$category_id) {
                $stmt = $conn->prepare("INSERT INTO category (name) VALUES (?)");
                $stmt->bind_param("s", $category_name);
                $stmt->execute();
                $category_id = $stmt->insert_id;
                $stmt->close();
            }

            // Insert new book data
            $stmt = $conn->prepare("INSERT INTO books (title, description, author_id, category_id, cover, file) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiiss", $title, $description, $author_id, $category_id, $cover_path, $file_path);

            if ($stmt->execute()) {
                header("Location: upload.php?success=Book added successfully!");
            } else {
                header("Location: upload.php?error=Failed to add book. Try again.");
            }
            $stmt->close();
            $conn->close();
            exit;
        } else {
            header("Location: upload.php?error=Failed to upload files!");
            exit;
        }
    } else {
        header("Location: upload.php?error=Please upload all files.");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Book - Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --primary-color: #dc143c;
            --hover-color: rgb(181, 5, 40);
            --bg-color: #f5f5f5;
            --card-color: #ffffff;
            --text-color: #333;
            --border-radius: 10px;
        }

        * {
            box-sizing: border-box;
            padding: 0;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
        }

        .navbar {
            background-color: var(--primary-color);
            padding: 1rem 2rem;
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        .content {
            flex: 1;
            padding: 20px;
        }

        .card {
            background-color: var(--card-color);
            padding: 30px;
            border-radius: var(--border-radius);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            max-width: 700px;
            margin: 50px auto;
            width: 100%;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
        }

        input[type="text"],
        textarea,
        select,
        input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: var(--border-radius);
        }

        button {
            width: 100%;
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px;
            border-radius: var(--border-radius);
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: var(--hover-color);
        }
    </style>
</head>

<body>
    <div class="navbar">📚 Open Book — Admin Panel</div>
    <div class="container">
        <div class="content">
            <div class="card">
                <h2>Add New Book</h2>
                <?php if (isset($_GET['success'])): ?>
                    <div style="padding: 15px; background-color: #d4edda; color: #155724; border-radius: var(--border-radius); margin-bottom: 20px; border: 1px solid #c3e6cb;">
                        <?= htmlspecialchars($_GET['success']) ?> 🎉<br>
                        <a href="books.php" style="color: var(--primary-color); text-decoration: underline;">Go to the books page →</a>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div style="padding: 15px; background-color: #f8d7da; color: #721c24; border-radius: var(--border-radius); margin-bottom: 20px; border: 1px solid #f5c6cb;">
                        <?= htmlspecialchars($_GET['error']) ?>
                    </div>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="book_title">Book Title</label>
                        <input type="text" id="book_title" name="book_title" required>
                    </div>
                    <div class="form-group">
                        <label for="book_description">Book Description</label>
                        <textarea id="book_description" name="book_description" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="book_author">Book Author</label>
                        <input type="text" id="book_author" name="book_author" required>
                    </div>
                    <div class="form-group">
                        <label for="book_category">Book Category</label>
                        <input type="text" id="book_category" name="book_category" required>
                    </div>
                    <div class="form-group">
                        <label for="book_cover">Book Cover</label>
                        <input type="file" id="book_cover" name="book_cover" accept="image/*" required>
                    </div>
                    <div class="form-group">
                        <label for="file">Book File (PDF)</label>
                        <input type="file" id="file" name="file" accept="application/pdf" required>
                    </div>
                    <button type="submit">Add Book</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>