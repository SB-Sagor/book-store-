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
    $isbn = htmlspecialchars($_POST["book_isbn"]);
    $description = htmlspecialchars($_POST["book_description"]);
    $author_id = intval($_POST["book_author"]);
    $category_id = intval($_POST["book_category"]);

    if (isset($_FILES["book_cover"]) && isset($_FILES["file"])) {
        $cover_name = $_FILES["book_cover"]["name"];
        $cover_tmp = $_FILES["book_cover"]["tmp_name"];
        $cover_ext = strtolower(pathinfo($cover_name, PATHINFO_EXTENSION));

        $file_name = $_FILES["file"]["name"];
        $file_tmp = $_FILES["file"]["tmp_name"];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($cover_ext, ["jpg", "jpeg", "png"]) && $file_ext === "pdf") {
            $coverDir = "uploads/covers/";
            $bookDir = "uploads/books/";

            if (!is_dir($coverDir)) mkdir($coverDir, 0755, true);
            if (!is_dir($bookDir)) mkdir($bookDir, 0755, true);

            $cover_path = $coverDir . uniqid() . "_" . basename($cover_name);
            $file_path = $bookDir . uniqid() . "_" . basename($file_name);

            if (move_uploaded_file($cover_tmp, $cover_path) && move_uploaded_file($file_tmp, $file_path)) {
                $sql = "INSERT INTO books (title, isbn, description, author_id, category_id, cover, file) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssisss", $title, $isbn, $description, $author_id, $category_id, $cover_path, $file_path);

                if ($stmt->execute()) {
                    header("Location: add-books.php?success=Book added successfully!");
                    exit;
                } else {
                    header("Location: add-books.php?error=Failed to add book.");
                    exit;
                }
            } else {
                header("Location: add-books.php?error=Upload failed.");
                exit;
            }
        } else {
            header("Location: add-books.php?error=Invalid file type.");
            exit;
        }
    } else {
        header("Location: add-books.php?error=Files are missing.");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Book - Admin Panel</title>
    <link rel="stylesheet" href="addbooks.css">
</head>

<body>
    <div class="navbar">📚 Open Book — Admin Panel</div>

    <div class="container">
        <aside class="sidebar">
            <ul>
                <li onclick="window.location.href='add-author.php'">✍️ Add Author</li>
                <li onclick="window.location.href='add-books.php'">📚 Add Books</li>
                <li onclick="window.location.href='add-category.php'">📂 Add Category</li>
                <li onclick="window.location.href='../index.php'">📨 Book Requests</li>
                <li onclick="window.location.href='../books.php'">📚 All Books</li>
                <li onclick="window.location.href='admin.php'">👤 Manage Users</li>
                <li onclick="window.location.href='settings.php'">⚙️ Settings</li>
                <li onclick="window.location.href='logout.php'">🚪 Logout</li>
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
                        <label for="book_isbn">ISBN</label>
                        <input type="text" id="book_isbn" name="book_isbn" required>
                    </div>
                    <div class="form-group">
                        <label for="book_description">Book Description</label>
                        <textarea id="book_description" name="book_description" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="book_author">Author</label>
                        <select id="book_author" name="book_author" required>
                            <option value="">Select author</option>
                            <?php while ($row = $authorResult->fetch_assoc()): ?>
                                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="book_category">Category</label>
                        <select id="book_category" name="book_category" required>
                            <option value="">Select category</option>
                            <?php while ($row = $categoryResult->fetch_assoc()): ?>
                                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="book_cover">Book Cover (JPG/PNG)</label>
                        <input type="file" id="book_cover" name="book_cover" accept="image/*" required>
                    </div>
                    <div class="form-group">
                        <label for="file">Book PDF</label>
                        <input type="file" id="file" name="file" accept="application/pdf" required>
                    </div>
                    <button type="submit">Add Book</button>
                </form>

                <!-- 🖼️ Show All Books -->
                <h2 style="margin-top:40px;">📘 Recently Added Books</h2>
                <?php
                $bookQuery = "
                    SELECT books.title, books.isbn, books.cover, authors.name AS author_name, category.name AS category_name
                    FROM books
                    LEFT JOIN authors ON books.author_id = authors.id
                    LEFT JOIN category ON books.category_id = category.id
                    ORDER BY books.id DESC
                ";
                $bookResult = $conn->query($bookQuery);
                ?>

                <?php if ($bookResult->num_rows > 0): ?>
                    <table class="book-table">
                        <thead>
                            <tr>
                                <th>🖼️ Cover</th>
                                <th>📘 Title</th>
                                <th>🔢 ISBN</th>
                                <th>✍️ Author</th>
                                <th>📂 Category</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $bookResult->fetch_assoc()): ?>
                                <tr>
                                    <td style="text-align: center;">
                                        <?php if (file_exists($row['cover'])): ?>
                                            <img src="<?= $row['cover'] ?>" class="book-cover" style="height: 60px;">
                                        <?php else: ?>
                                            <span style="color:red;">Not found</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['title']) ?></td>
                                    <td><?= htmlspecialchars($row['isbn']) ?></td>
                                    <td><?= htmlspecialchars($row['author_name']) ?></td>
                                    <td><?= htmlspecialchars($row['category_name']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No books found yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>