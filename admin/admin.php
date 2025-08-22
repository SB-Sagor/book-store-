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
    <link rel="stylesheet" href="admin.css">
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
                                <a href="delete-author.php?id=<?= $row['id']; ?>"
                                    onclick="return confirm('Are you sure?')">🗑️ Delete</a>
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
                                <a href="delete-category.php?id=<?= $row['id']; ?>"
                                    onclick="return confirm('Are you sure?')">🗑️ Delete</a>
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
                                    <img src="<?= '../' . htmlspecialchars($row['cover']); ?>" alt="Cover" width="50">
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['author_name']); ?></td>
                            <td><?= htmlspecialchars($row['description']); ?></td>
                            <td><?= htmlspecialchars($row['category_name']); ?></td>
                            <td>
                                <a href="update-books.php?id=<?= $row['id']; ?>">✏️ Edit</a>
                                <a href="delete-books.php?id=<?= $row['id']; ?>"
                                    onclick="return confirm('Are you sure you want to delete this book?')">🗑️ Delete</a>
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