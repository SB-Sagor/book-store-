<?php
session_start();
include 'db.php'; // Ensure the database connection file is included

// Initialize notification message
$notification = '';

// Display logout notification if it exists
if (isset($_SESSION['temp_message'])) {
    $notification = $_SESSION['temp_message'];
    unset($_SESSION['temp_message']); // Remove the message after displaying
}

// Pagination Setup
$limit = 10; // Items per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch books based on search criteria
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $stmt = $conn->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ? LIMIT ? OFFSET ?");
    $searchTerm = '%' . $search . '%';
    $stmt->bind_param("ssii", $searchTerm, $searchTerm, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $stmt = $conn->prepare("SELECT * FROM books LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
}

// Fetch total books for pagination
$totalBooks = $conn->query("SELECT COUNT(*) AS count FROM books")->fetch_assoc()['count'];
$totalPages = ceil($totalBooks / $limit);

// Fetch book requests
$request_result = $conn->query("SELECT book_requests.id, book_requests.book_title, book_requests.request_details, users.email FROM book_requests JOIN users ON book_requests.user_id = users.id");

// Fetch book replies
$replies_result = $conn->query("SELECT * FROM book_replies");
$replies = [];
while ($row = $replies_result->fetch_assoc()) {
    $replies[$row['request_id']][] = $row;
}

// Handle book request replies
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_reply'])) {
    $request_id = $_POST['reply_request_id'];
    $reply_details = $_POST['reply_details'];

    $target_dir = "replies/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_name = basename($_FILES["reply_file"]["name"]);
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["reply_file"]["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO book_replies (request_id, reply_details, file_path) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $request_id, $reply_details, $target_file);

        if ($stmt->execute()) {
            $notification = "Reply submitted!";
        } else {
            $notification = "Error: " . $stmt->error;
        }
    } else {
        $notification = "File upload failed!";
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Book Platform</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
    :root {
        --primary-color: #dc143c;
        --hover-color: rgb(181, 5, 40);
        --bg-light: #ffffff;
        --bg-secondary: #f9f9f9;
        --text-color: #333;
        --border-radius: 8px;
    }

    * {
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', sans-serif;
        margin: 0;
        padding: 0;
        background-color: var(--bg-secondary);
        color: var(--text-color);
    }

    .navbar {
        background-color: var(--primary-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        flex-wrap: wrap;
        position: relative;

    }

    .navbar a {
        color: white;
        text-decoration: none;
        margin-right: 15px;
        padding: 10px 15px;
        border-radius: var(--border-radius);
        transition: background 0.3s;
    }

    .navbar a:hover {
        background-color: var(--hover-color);
    }

    .search-container {
        margin-top: 10px;
    }

    .search-container input[type=text] {
        padding: 8px;
        border-radius: var(--border-radius);
        border: 1px solid #ccc;
        font-size: 14px;
    }

    .container {
        padding: 20px;
        max-width: 1100px;
        margin: auto;
    }

    h2 {
        border-bottom: 2px solid #ddd;
        padding-bottom: 5px;
    }

    .notification {
        margin-bottom: 20px;
        background-color: #e6ffe6;
        color: green;
        padding: 10px 15px;
        border: 1px solid green;
        border-radius: var(--border-radius);
    }

    .book-section,
    .request-section {
        background-color: var(--bg-light);
        border-radius: var(--border-radius);
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
    }

    .book-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }

    .download-btn {
        background-color: var(--primary-color);
        color: white;
        padding: 8px 14px;
        text-decoration: none;
        font-size: 14px;
        border-radius: var(--border-radius);
    }

    .download-btn:hover {
        background-color: var(--hover-color);
    }

    .reply-form {
        margin-top: 10px;
    }

    .reply-form textarea,
    .reply-form input[type="file"] {
        width: 100%;
        margin-top: 10px;
        padding: 10px;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: var(--border-radius);
    }

    .reply-form button {
        background-color: var(--primary-color);
        color: white;
        border: none;
        margin-top: 10px;
        padding: 8px 16px;
        font-size: 14px;
        border-radius: var(--border-radius);
        cursor: pointer;
    }

    .reply-form button:hover {
        background-color: var(--hover-color);
    }

    .pagination a {
        padding: 8px 12px;
        margin: 0 4px;
        background-color: var(--primary-color);
        color: white;
        text-decoration: none;
        border-radius: var(--border-radius);
        transition: background-color 0.3s ease;
    }

    .pagination a:hover {
        background-color: var(--hover-color);
    }

    .menu-toggle {
        display: none;
        font-size: 24px;
        background: none;
        color: white;
        border: none;
        cursor: pointer;
    }

    .sidebar {
        display: flex;
        gap: 10px;
    }

    .sidebar a {
        color: white;
        text-decoration: none;
        padding: 10px 15px;
        border-radius: var(--border-radius);
        transition: background 0.3s;
    }

    .sidebar a:hover {
        background-color: var(--hover-color);
    }

    @media (max-width: 768px) {
        .book-item {
            flex-direction: column;
            align-items: flex-start;
        }

        .navbar {
            flex-direction: row;
            align-items: flex-start;
        }

        .navbar a {
            margin: 5px 0;
        }

        .menu-toggle {
            display: block;
        }

        .sidebar {
            display: none;
            flex-direction: column;
            background-color: var(--primary-color);
            width: 100%;
            position: absolute;
            top: 70px;
            left: 0px;
            z-index: 10;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            padding-bottom: 10px;
            animation: slideDown 0.3s ease;
        }

        .sidebar a {
            margin: 5px 20px;
        }

        .sidebar.active {
            display: flex;
        }

        @keyframes slideDown {
            from {
                opacity: 1;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(10);
            }
        }
    }
    </style>

</head>

<body>
    <div class="navbar">
        <button class="menu-toggle">&#9776;</button> <!-- Hamburger icon -->

        <div class="sidebar" id="sidebar">
            <a href="login.php">👤 Account</a>
            <a href="books.php">📚 All-Books</a>
            <a href="upload.php">📤 Upload</a>
            <a href="request.php">📝 Request</a>
            <a href="logout.php">🔓 Logout</a>
        </div>

        <div class="search-container">
            <form method="GET">
                <input type="text" placeholder="Search for books.." name="search">
                <button type="submit" class="download-btn">Search</button>
            </form>
        </div>
    </div>
    <!-- notification -->
     <!-- login -->
    <?php if (isset($_SESSION['success_msg'])) : ?>
    <div id="flashMsg" class="toast"><?= htmlspecialchars($_SESSION['success_msg']) ?></div>
    <script>
    setTimeout(() => {
        const msg = document.getElementById("flashMsg");
        if (msg) {
            msg.classList.add('fade-out');
            setTimeout(() => msg.remove(), 500);
        }
    }, 1000);
    </script>
    <!-- logout -->
    <?php unset($_SESSION['success_msg']); ?>
    <?php endif; ?>
    <?php

if (isset($_SESSION['success_msg'])) {
    echo "<div id='flashMsg' class='toast'>" . htmlspecialchars($_SESSION['success_msg']) . "</div>";
    unset($_SESSION['success_msg']);

    //  Session destroy after message shown
    session_unset();
    session_destroy();
}
?>



    <!-- Book Section -->
    <div class="container">
        <?php if ($notification) : ?>
        <div class="notification"><?= htmlspecialchars($notification) ?></div>
        <?php endif; ?>

        <div class="book-section">
            <h2>Available Books</h2>
            <!-- download books -->
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='book-item'>";
                    echo "<p><strong>Title:</strong> " . htmlspecialchars($row['title']) . " | <strong>Author:</strong> " . htmlspecialchars($row['author']) . "</p>";
                    echo "<a href='" . htmlspecialchars($row['file_path']) . "' download class='download-btn'>Download</a>";
                    echo "</div>";
                }
            } else {
                echo "<p>No books found</p>";
            }
            ?>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
            <a href="?page=<?= $i ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>

        <!-- Book Requests Section -->
        <div class="request-section">
            <h2>Book Requests</h2>
            <?php
            if ($request_result->num_rows > 0) {
                while ($row = $request_result->fetch_assoc()) {
                    echo "<p><strong>Book Title:</strong> " . htmlspecialchars($row['book_title']) . " | <strong>Requested By:</strong> " . htmlspecialchars($row['email']) . "<br><strong>Details:</strong> " . htmlspecialchars($row['request_details']) . "</p>";

                    // Display replies if available
                    if (isset($replies[$row['id']])) {
                        echo "<h4>Replies:</h4>";
                        foreach ($replies[$row['id']] as $reply) {
                            echo "<p>" . htmlspecialchars($reply['reply_details']);
                            if ($reply['file_path']) {
                                echo "<br><a href='" . htmlspecialchars($reply['file_path']) . "' download class='download-btn'>Download</a>";
                            }
                            echo "</p>";
                        }
                    }

                    // Reply form
                    echo '<div class="reply-form">';
                    echo '<form method="POST" enctype="multipart/form-data">';
                    echo '<textarea name="reply_details" placeholder="Provide the book or details..." required></textarea>';
                    echo '<input type="file" name="reply_file" required>';
                    echo '<input type="hidden" name="reply_request_id" value="' . htmlspecialchars($row['id']) . '">';
                    echo '<button type="submit" name="submit_reply">Reply</button>';
                    echo '</form>';
                    echo '</div>';
                }
            } else {
                echo "<p>No book requests found</p>";
            }
            ?>
        </div>
    </div>

    <!-- Footer Section -->
    <footer class="navbar">
        <p>&copy; <a href="admin/index.php">2025 </a>Book Platform. All rights reserved.</p>
    </footer>
    <script>
    const toggleBtn = document.querySelector('.menu-toggle');
    const sidebar = document.getElementById('sidebar');

    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('active');
    });
    </script>

</body>

</html>