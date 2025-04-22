<?php
session_start();
include "db_conn.php";


// Fetch authors
$authorQuery = "SELECT * FROM authors";
$authorResult = $conn->query($authorQuery);

// Fetch categories
$categoryQuery = "SELECT * FROM category";
$categoryResult = $conn->query($categoryQuery);

// Fetch books
$booksQuery = "
    SELECT 
        books.id, 
        books.title, 
        books.cover,
        books.description, 
        authors.name AS author_name, 
        category.name AS category_name 
    FROM books
    LEFT JOIN authors ON books.author_id = authors.id
    LEFT JOIN category ON books.category_id = category.id
";
$booksresult = $conn->query($booksQuery);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
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
            margin: 0;
            padding: 0;
            box-sizing: border-box;
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1100;
        }

        .hamburger {
            font-size: 1.8rem;
            cursor: pointer;
            display: none;
        }

        .container {
            display: flex;
            min-height: 100vh;
            padding-top: 60px;
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

        .sidebar li:hover {
            background-color: #a90328;
        }

        .content {
            flex: 1;
            padding: 30px;
        }

        .card {
            background-color: var(--card-color);
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: var(--primary-color);
            color: white;
        }

        a {
            color: var(--primary-color);
            text-decoration: none;
            margin-right: 10px;
        }

        a:hover {
            color: var(--hover-color);
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .sidebar {
                position: fixed;
                top: 60px;
                left: -220px;
                width: 220px;
                height: calc(100vh - 60px);
                background-color: var(--primary-color);
                z-index: 1000;
                transition: left 0.3s ease;
            }

            .sidebar.show {
                left: 0;
            }

            .hamburger {
                display: block;
                color: white;
            }

            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            .content {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="navbar">
        <span class="hamburger" onclick="toggleSidebar()">☰</span>
        📚 Open Book — Admin Panel
    </div>

    <div class="container">
        <aside class="sidebar" id="sidebar">
            <ul>

                <li onclick="navigateTo('add-author.php')">✍️ Add Author</li>
                <li onclick="navigateTo('add-category.php')">📂 Add Category</li>
                <li onclick="navigateTo('add-books.php')">📖 Add Books</li>
                <li onclick="navigateTo('../books.php')">📚All-Books</li>
                <li onclick="navigateTo('../index.php')">📨 Book Requests</li>
                <li onclick="navigateTo('admin.php')">👤 Manage Users</li>
                <li onclick="navigateTo('settings.php')">⚙️ Settings</li>
                <li onclick="navigateTo('logout.php')">🚪 Logout</li>
            </ul>
        </aside>

        <main class="content">
            <div class="card">
                <h2>📖 Author List</h2>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Author Name</th>
                        <th>Actions</th>
                    </tr>
                    <?php while ($row = $authorResult->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id']; ?></td>
                            <td><?= htmlspecialchars($row['name']); ?></td>
                            <td>
                                <a href="update-author.php?id=<?= $row['id']; ?>">✏️ Edit</a>
                                <a href="delete-author.php?id=<?= $row['id']; ?>" onclick="return confirm('Are you sure?')">🗑️ Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>

            <div class="card">
                <h2>📂 Category List</h2>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Category Name</th>
                        <th>Actions</th>
                    </tr>
                    <?php while ($row = $categoryResult->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id']; ?></td>
                            <td><?= htmlspecialchars($row['name']); ?></td>
                            <td>
                                <a href="update-category.php?id=<?= $row['id']; ?>">✏️ Edit</a>
                                <a href="delete-category.php?id=<?= $row['id']; ?>" onclick="return confirm('Are you sure?')">🗑️ Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>

            <div class="card">
                <h2>📚 Book List</h2>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Cover</th>
                        <th>Author</th>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Actions</th>
                    </tr>
                    <?php while ($row = $booksresult->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id']; ?></td>
                            <td><?= htmlspecialchars($row['title']); ?></td>
                            <td>
                                <?php if (!empty($row['cover'])): ?>
                                    <img src="<?= htmlspecialchars($row['cover']); ?>" alt="Cover" width="50">
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['author_name']); ?></td>
                            <td><?= htmlspecialchars($row['description']); ?></td>
                            <td><?= htmlspecialchars($row['category_name']); ?></td>
                            <td>
                                <a href="update-books.php?id=<?= $row['id']; ?>">✏️ Edit</a>
                                <a href="delete-books.php?id=<?= $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this book?')">🗑️ Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </main>
    </div>

    <script>
        function navigateTo(page) {
            window.location.href = page;
        }

        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("show");
        }
    </script>
</body>

</html>

<?php $conn->close(); ?>