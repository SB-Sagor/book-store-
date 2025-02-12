<?php
session_start();
include "db_conn.php"; // Ensure the correct path

// Fetch authors
$authorQuery = "SELECT * FROM authors";
$authorResult = $conn->query($authorQuery);

// Fetch categories
$categoryQuery = "SELECT * FROM category";
$categoryResult = $conn->query($categoryQuery);

// Fetch books with author and category names
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .navbar {
            background-color: crimson;
            color: white;
            padding: 0.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navtext {
            cursor: pointer;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .container {
            display: flex;
        }

        .admin {
            width: 20%;
            background-color: crimson;
            padding: 1rem;
            color: white;
            min-height: 100vh;
        }

        .admin-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .admin-list li {
            cursor: pointer;
            padding: 10px;
            background-color: rgb(200, 10, 50);
            color: white;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 10px;
            transition: background-color 0.3s ease;
        }

        .admin-list li:hover {
            background-color: rgb(190, 8, 44);
        }

        .content {
            width: 80%;
            padding: 20px;
        }

        .table-container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: crimson;
            color: white;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="navtext">Open Book - Admin Panel</div>
    </nav>

    <div class="container">
        <section class="admin">
            <ul class="admin-list">
                <li onclick="navigateTo('index.php')">View Books</li>
                <li onclick="navigateTo('add-author.php')">Add Author</li>
                <li onclick="navigateTo('add-books.php')">Add Books</li>
                <li onclick="navigateTo('add-category.php')">Add Category</li>
                <li onclick="navigateTo('requests.php')">Book Requests</li>
                <li onclick="navigateTo('manage-users.php')">Manage Users</li>
                <li onclick="navigateTo('settings.php')">Settings</li>
                <li onclick="navigateTo('logout.php')">Logout</li>
            </ul>
        </section>

        <section class="content">
            <!-- AUTHOR LIST -->
            <div class="table-container">
                <h2>Author List</h2>
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
                                <a href="update-author.php?id=<?= $row['id']; ?>">Edit</a>
                                <a href="delete-author.php?id=<?= $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>


            <!-- CATEGORY LIST -->
            <div class="table-container">
                <h2>Category List</h2>
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
                                <a href="update-category.php?id=<?= $row['id']; ?>">Edit</a>
                                <a href="delete-category.php?id=<?= $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
            <!-- BOOK LIST -->
            <div class="table-container">
                <h2>Book List</h2>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Book Name</th>
                        <th>Cover</th>
                        <th>Author</th>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Actions</th>

                    </tr>

                    <?php while ($row = $booksresult->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id']; ?></td>
                            <td><?= isset($row['title']) ? htmlspecialchars($row['title']) : 'N/A'; ?></td>
                            <td>


                            </td>
                            <td><?= isset($row['author_name']) ? htmlspecialchars($row['author_name']) : 'N/A'; ?></td>
                            <td><?= isset($row['description']) ? htmlspecialchars($row['description']) : 'N/A'; ?></td>
                            <td><?= isset($row['category_name']) ? htmlspecialchars($row['category_name']) : 'N/A'; ?></td>

                            <td>
                                <a href="update-books.php?id=<?= $row['id']; ?>">Edit</a>
                                <a href="delete-books.php?id=<?= $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this book?')">Delete</a>
                            </td>

                        </tr>
                    <?php endwhile; ?>

                </table>
            </div>
        </section>

    </div>

    <script>
        function navigateTo(page) {
            window.location.href = page;
        }
    </script>
</body>

</html>

<?php $conn->close(); ?>