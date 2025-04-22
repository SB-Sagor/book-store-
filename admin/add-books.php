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

        if (in_array($cover_ext, ["jpg", "jpeg", "png"]) && $file_ext === "pdf") {
            $cover_path = "uploads/covers/" . uniqid() . "_" . basename($cover_name);
            $file_path = "uploads/books/" . uniqid() . "_" . basename($file_name);

            // Ensure uploads folders exist
            $coverDir = "uploads/covers/";
            $bookDir = "uploads/books/";
            if (!is_dir($coverDir)) {
                mkdir($coverDir, 0755, true); // true = recursive
            }
            if (!is_dir($bookDir)) {
                mkdir($bookDir, 0755, true);
            }

            if (move_uploaded_file($cover_tmp, $cover_path) && move_uploaded_file($file_tmp, $file_path)) {
                $sql = "INSERT INTO books (title, description, author_id, category_id, cover, file) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssiiss", $title, $description, $author_id, $category_id, $cover_path, $file_path);

                if ($stmt->execute()) {
                    header("Location: add-books.php?success=Book added successfully!");
                    exit;
                } else {
                    header("Location: add-books.php?error=Failed to add book. Try again.");
                    exit;
                }
            } else {
                header("Location: add-books.php?error=Failed to upload files!");
                exit;
            }
        } else {
            header("Location: add-books.php?error=Invalid file type! Only JPG, PNG for cover and PDF for file.");
            exit;
        }
    } else {
        header("Location: add-books.php?error=Please upload all files.");
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

        .sidebar {
            width: 220px;
            background-color: var(--primary-color);
            color: white;
            padding: 20px;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar li {
            padding: 12px;
            margin-bottom: 10px;
            background-color: var(--hover-color);
            border-radius: var(--border-radius);
            cursor: pointer;
            text-align: center;
            transition: 0.3s ease;
        }

        .sidebar li:hover,
        .sidebar li.active {
            background-color: #fff;
            color: var(--primary-color);
            font-weight: bold;
        }

        .content {
            flex: 1;
            padding: 0px;

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

        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: var(--border-radius);
            font-weight: bold;
            text-align: center;
        }

        .alert.success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                display: flex;
                overflow-x: auto;
                padding: 10px;
            }

            .sidebar ul {
                display: flex;
                gap: 10px;
                flex-wrap: nowrap;
            }

            .sidebar li {
                white-space: nowrap;
                padding: 10px 20px;
            }
        }
    </style>
    <script>
        function navigateTo(page) {
            window.location.href = page;
        }

        window.onload = () => {
            const current = window.location.pathname.split("/").pop();
            document.querySelectorAll(".sidebar li").forEach(li => {
                if (li.getAttribute("onclick")?.includes(current)) {
                    li.classList.add("active");
                }
            });
        }
    </script>
</head>

<body>
    <div class="navbar">📚 Open Book — Admin Panel</div>

    <div class="container">
        <aside class="sidebar">
            <ul>
                <li onclick="navigateTo('add-author.php')">✍️ Add Author</li>
                <li onclick="navigateTo('add-books.php')">📚 Add Books</li>
                <li onclick="navigateTo('add-category.php')">📂 Add Category</li>
                <li onclick="navigateTo('../index.php')">📨 Book Requests</li>
                <li onclick="navigateTo('admin.php')">👤 Manage Users</li>
                <li onclick="navigateTo('settings.php')">⚙️ Settings</li>
                <li onclick="navigateTo('logout.php')">🚪 Logout</li>
            </ul>
        </aside>

        <div class="content">
            <div class="card">
                <h2>Add New Book</h2>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert error"><?= htmlspecialchars($_GET['error']) ?></div>
                <?php endif; ?>
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert success"><?= htmlspecialchars($_GET['success']) ?></div>
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
                        <select id="book_author" name="book_author" required>
                            <option value="">Select author</option>
                            <?php while ($row = $authorResult->fetch_assoc()) : ?>
                                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="book_category">Book Category</label>
                        <select id="book_category" name="book_category" required>
                            <option value="">Select category</option>
                            <?php while ($row = $categoryResult->fetch_assoc()) : ?>
                                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
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