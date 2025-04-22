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
            padding: 30px;
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
            margin-bottom: 20px;
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