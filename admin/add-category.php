<?php
include "db_conn.php"; // Ensure correct path

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["category_name"])) {
    $category_name = trim($_POST["category_name"]);

    if (!empty($category_name)) {
        $stmt = $conn->prepare("INSERT INTO category (name) VALUES (?)");
        $stmt->bind_param("s", $category_name);
        if ($stmt->execute()) {
            echo "<script>alert('Category added successfully!'); window.location.href='admin.php';</script>";
        } else {
            echo "<script>alert('Error adding category!');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Category name cannot be empty!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Author - Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin.css">

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
        <!-- Sidebar -->
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

        <!-- Main Content -->
        <main class="content">
            <div class="card">
                <h2>📂 Add New Category</h2>
                <form action="add-category.php" method="POST">
                    <label for="category_name">Category Name</label>
                    <input type="text" id="category_name" name="category_name" required>
                    <button type="submit">Add Category</button>
                </form>
            </div>
        </main>
    </div>

    <script>
        function navigateTo(page) {
            window.location.href = page;
        }
    </script>
</body>

</html>