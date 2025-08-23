<?php
session_start();
include 'admin/db_conn.php';
// if (file_exists('admin/uploads/avatars/default-avatar.png')) {
//     echo "Avatar exists!";
// } else {
//     echo "Avatar NOT found!";
// }
// Notifications
$notification = '';
if (isset($_SESSION['temp_message'])) {
    $notification = $_SESSION['temp_message'];
    unset($_SESSION['temp_message']);
}

// Profile Fetch
$profile = null;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT name, email, avatar, coins FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $profile = $stmt->get_result()->fetch_assoc();
}

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchClean = str_replace("-", "", $search);
$result = false;

if (!empty($search)) {
    $searchTitle = '%' . $search . '%';
    $searchAuthor = '%' . $search . '%';
    $searchISBN = '%' . $searchClean . '%';
    $stmt = $conn->prepare("
        SELECT books.*, authors.name AS author_name
        FROM books
        LEFT JOIN authors ON books.author_id = authors.id
        WHERE books.title LIKE ?
            OR authors.name LIKE ?
            OR REPLACE(books.isbn_raw, '-', '') LIKE ?
        LIMIT ? OFFSET ?
    ");
    $stmt->bind_param("sssii", $searchTitle, $searchAuthor, $searchISBN, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
}

// Requests and replies
$request_result = $conn->query("
    SELECT book_requests.id, book_requests.book_title, book_requests.request_details, book_requests.isbn_raw, users.email 
    FROM book_requests 
    JOIN users ON book_requests.user_id = users.id
");

$replies_result = $conn->query("SELECT * FROM book_replies");
$replies = [];
while ($row = $replies_result->fetch_assoc()) {
    $replies[$row['request_id']][] = $row;
}

// Handle reply submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_reply'])) {
    $request_id = $_POST['reply_request_id'];
    $reply_details = $_POST['reply_details'];
    $target_dir = "replies/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
    $file_name = time() . "_" . basename($_FILES["reply_file"]["name"]);
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["reply_file"]["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO book_replies (request_id, reply_details, file_path) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $request_id, $reply_details, $target_file);
        $notification = $stmt->execute() ? "Reply submitted!" : "Error: " . $stmt->error;
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
    <link rel="stylesheet" href="avatars.css">
    <link rel="stylesheet" href="index.css">

</head>

<body>
    <div class="navbar">
        <button class="menu-toggle">&#9776;</button>
        <div class="sidebar" id="sidebar">
            <a href="login.php">👤 Account</a>
            <a href="books.php">📚 All-Books</a>
            <a href="upload.php">📤 Upload</a>
            <a href="request.php">📝 Request</a>
            <a href="logout.php">🔓 Logout</a>
        </div>

        <?php if ($profile): ?>
            <div class="avatar-wrapper" onclick="toggleProfileDropdown(event)">
                <img src="admin/uploads/avatars/<?= htmlspecialchars($profile['avatar'] ?: 'default-avatar.png') ?>"
                    alt="<?= htmlspecialchars($profile['name']) ?>'s Avatar" class="avatar-icon-small">
                <div class="profile-dropdown" id="profileDropdown">
                    <h4><?= htmlspecialchars($profile['name']) ?></h4>
                    <p><?= htmlspecialchars($profile['email']) ?></p>
                    <p title="Your current coin balance">💰 Coins: <?= htmlspecialchars($profile['coins']) ?></p>
                    <hr>
                </div>
            </div>
        <?php endif; ?>

        <div class="search-container">
            <form method="GET">
                <input type="text" placeholder="Search by isbn, author, or title..." name="search"
                    value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="download-btn">Search</button>
            </form>
        </div>

    </div>

    <?php if ($notification): ?>
        <div class="notification"><?= htmlspecialchars($notification) ?></div>
    <?php endif; ?>


    <div class="container">
        <?php if (!empty($search)): ?>
            <div class="book-section">
                <h2>Search Results</h2>
                <?php if ($result && $result->num_rows > 0): ?>
                    <table class="book-table">
                        <thead>
                            <tr>
                                <th>Cover</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>ISBN</th>
                                <th>Download</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="cover-cell">
                                        <img src="<?= htmlspecialchars($row['cover']) ?>" alt="Cover" class="book-cover">
                                    </td>
                                    <td><?= htmlspecialchars($row['title']) ?></td>
                                    <td><?= htmlspecialchars($row['author_name']) ?></td>
                                    <td><?= htmlspecialchars($row['isbn']) ?></td>
                                    <td><a href="<?= htmlspecialchars($row['file']) ?>" class="download-btn" target="_blank"
                                            download>Download</a></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No books found for "<?= htmlspecialchars($search) ?>"</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="request-section">
            <h2>Book Requests</h2>
            <?php if ($request_result->num_rows > 0): ?>
                <?php while ($row = $request_result->fetch_assoc()): ?>
                    <div class="request-block">
                        <p><strong>Book Title:</strong> <?= htmlspecialchars($row['book_title']) ?> (ISBN:
                            <?= htmlspecialchars($row['isbn_raw']) ?>) |
                            <strong>Requested By:</strong> <?= htmlspecialchars($row['email']) ?><br>
                            <strong>Details:</strong> <?= htmlspecialchars($row['request_details']) ?>
                        </p>

                        <?php if (isset($replies[$row['id']])): ?>
                            <h4>Replies:</h4>
                            <?php foreach ($replies[$row['id']] as $reply): ?>
                                <p><?= htmlspecialchars($reply['reply_details']) ?>
                                    <?php if ($reply['file_path']): ?>
                                        <br><a href="<?= htmlspecialchars($reply['file_path']) ?>" download
                                            class="download-btn">Download</a>
                                    <?php endif; ?>
                                </p>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <div class="reply-form">
                            <form method="POST" enctype="multipart/form-data">
                                <textarea name="reply_details" required placeholder="Provide the book or details..."></textarea>
                                <input type="file" name="reply_file" required>
                                <input type="hidden" name="reply_request_id" value="<?= htmlspecialchars($row['id']) ?>">
                                <button type="submit" name="submit_reply">Reply</button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No book requests found.</p>
            <?php endif; ?>
        </div>
    </div>
    <script>
        function toggleProfileDropdown(event) {
            event.stopPropagation(); // Prevent window click from closing immediately
            document.getElementById('profileDropdown').classList.toggle('active');
        }

        window.addEventListener('click', function(e) {
            const dropdown = document.getElementById('profileDropdown');
            const avatar = document.querySelector('.avatar-wrapper');
            if (dropdown && !avatar.contains(e.target)) {
                dropdown.classList.remove('active');
            }
        });
    </script>


</body>

</html>