<?php
session_start();
include "admin/db_conn.php";

function validate_and_create_dir($path)
{
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
}

function validate_uploaded_file($file, $allowed_types, $max_size = PHP_INT_MAX)
{
    $file_ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    if (!in_array($file_ext, $allowed_types)) {
        return "Invalid file type! Only " . implode(", ", $allowed_types) . " allowed.";
    }
    if ($file["size"] > $max_size) {
        return "File size exceeds the server limit.";
    }
    return null;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = htmlspecialchars($_POST["book_title"]);
    $description = htmlspecialchars($_POST["book_description"]);
    $author_name = htmlspecialchars($_POST["book_author"]);
    $category_name = htmlspecialchars($_POST["book_category"]);
    $isbn_raw = htmlspecialchars($_POST["book_isbn"]);
    $isbn_clean = str_replace("-", "", $isbn_raw);

    // Validate ISBN format
    if (!preg_match('/^\d{10}$|^\d{13}$/', $isbn_clean)) {
        header("Location: upload.php?error=Invalid ISBN format!");
        exit;
    }

    // Check for duplicate ISBN before any processing
    $isbn_stmt = $conn->prepare("SELECT title FROM books WHERE isbn = ?");
    $isbn_stmt->bind_param("s", $isbn_clean);
    $isbn_stmt->execute();
    $isbn_stmt->store_result();

    if ($isbn_stmt->num_rows > 0) {
        $isbn_stmt->bind_result($existing_title);
        $isbn_stmt->fetch();
        $isbn_stmt->close();

        // ISBN already exists — block upload and redirect with warning
        header("Location: upload.php?error=ISBN already exists! The book titled '$existing_title' is already registered.");
        exit;
    }
    $isbn_stmt->close();

    if (isset($_FILES["book_cover"]) && isset($_FILES["file"])) {
        $cover_error = validate_uploaded_file($_FILES["book_cover"], ["jpg", "jpeg", "png"]);
        $file_error = validate_uploaded_file($_FILES["file"], ["pdf"]);

        if ($cover_error || $file_error) {
            header("Location: upload.php?error=" . htmlspecialchars($cover_error ?: $file_error));
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
            // Handle author
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

            // Handle category
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

            // Final book insert
            $stmt = $conn->prepare("INSERT INTO books (title, description, author_id, category_id, cover, file, isbn, isbn_raw) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiissss", $title, $description, $author_id, $category_id, $cover_path, $file_path, $isbn_clean, $isbn_raw);


            if ($stmt->execute()) {
                header("Location: upload.php?success=Book added successfully!");
            } else {
                header("Location: upload.php?error=Database error! Book not added.");
            }
            $stmt->close();
            $conn->close();
            exit;
        } else {
            header("Location: upload.php?error=File upload failed!");
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
    <link rel="stylesheet" href="upload.css">
</head>

<body>
    <div class="navbar">
        <a href="books.php" style="color: white; text-decoration: none;">📚 Open Book Admin</a>
    </div>
    <div class="container">
        <div class="content">
            <div class="card">
                <h2>Add New Book</h2>
                <?php if (isset($_GET['success'])): ?>
                    <div
                        style="padding: 15px; background-color: #d4edda; color: #155724; border-radius: var(--border-radius); margin-bottom: 20px; border: 1px solid #c3e6cb;">
                        <?= htmlspecialchars($_GET['success']) ?> 🎉<br>
                        <a href="books.php" style="color: var(--primary-color); text-decoration: underline;">Go to the books
                            page →</a>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div
                        style="padding: 15px; background-color: #f8d7da; color: #721c24; border-radius: var(--border-radius); margin-bottom: 20px; border: 1px solid #f5c6cb;">
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
                    <div class="form-group"> <label for="book_isbn">Book ISBN (Hyphenated or Plain)</label> <input
                            type="text" id="book_isbn" name="book_isbn" pattern="^(\d{10}|\d{13}|(?:\d+-){3,}\d+)$"
                            title="Enter ISBN-10 or ISBN-13 with or without hyphens" required> </div>
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