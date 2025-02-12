<?php
include 'db.php';

// Initialize notification message
$notification = '';

// Fetch books based on search criteria
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['search'])) {
    $search = $_GET['search'];
    $stmt = $conn->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ?");
    $searchTerm = "%$search%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM books");
}

// Fetch book requests
$request_result = $conn->query("SELECT book_requests.id, book_requests.book_title, book_requests.request_details, users.email FROM book_requests JOIN users ON book_requests.user_id = users.id");

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

    // Redirect to the same page to avoid resubmission on refresh
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Fetch book replies
$replies_result = $conn->query("SELECT * FROM book_replies");
$replies = [];
while ($row = $replies_result->fetch_assoc()) {
    $replies[$row['request_id']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Book Platform</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: crimson;
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
        }

        .navbar a {
            color: white;
            border-radius: 10px;
            text-decoration: none;
            padding: 14px 20px;
            display: inline-block;
        }

        .navbar a:hover {
            background-color: rgb(235, 10, 63);
            color: black;
        }

        .search-container {
            display: flex;
            align-items: center;
        }

        .search-container input[type=text] {
            padding: 6px;
            margin-right: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .container {
            /* padding: 20px; */
        }

        h2 {
            color: #333;
        }

        .reply-form {
            margin-top: 10px;
        }

        .reply-form textarea,
        .reply-form input[type="file"] {
            width: 60%;
            padding: 6px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .reply-form button {
            padding: 6px 12px;
            background-color: crimson;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 5px;
        }

        .reply-form button:hover {
            background-color: rgb(255, 145, 0);
        }

        .notification {
            color: green;
            font-size: 16px;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid green;
            background-color: #e6ffe6;
            border-radius: 5px;
        }

        .error {
            color: red;
            font-size: 16px;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid red;
            background-color: #ffe6e6;
            border-radius: 5px;
        }
        .bookdiv {
            background-color: #e6ffe6;
            width: 90%;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: inline-block;
            padding: 14px 20px;
            margin: 0px 0px;
        }

        .book-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .book-item p {
            margin: 0;
            flex-grow: 1;
        }

        .download-btn {
            padding: 6px 12px;
            background-color: crimson;
            color: white;
            font-size: 12px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .download-btn:hover {
            background-color: rgb(255, 145, 0);
        }

        .requestdiv {
            background-color: #f4f4f4;
            width: 90%;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: inline-block;
            padding: 14px 20px;
            margin: 0px 0px;
        }
        
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const notification = document.querySelector('.notification');
            if (notification) {
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 5000); // Hide notification after 3 seconds
            }
        });
    </script>
</head>

<body>
    <div class="navbar">
        <div>
            <a href="login.php">Account</a>
            <a href="upload.php">Upload</a>
            <a href="request.php">Request</a>
            <a href="logout.php" >Logout</a>

        </div>
        <div class="search-container">
            <form method="GET">
                <input type="text" placeholder="Search for books.." name="search">
                <button type="submit">Search</button>
            </form>
        </div>
    </div>
    
    <div class="container">
        <?php
        // Display notification
        if ($notification) {
            echo '<div class="notification">' . $notification . '</div>';
        }
        ?>
        <div class="bookdiv">
            <!-- Available Books Section -->
            <h2>Available Books</h2>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='book-item'>";
                    echo "<p>Title: " . $row['title'] . " | Author: " . $row['author'] . "</p>";
                    echo "<a href='" . $row['file_path'] . "' download class='download-btn'>Download</a>";
                    echo "</div>";
                }
            } else {
                echo "<p>No books found</p>";
            }
            ?>
        </div>
        <div class="requestdiv">
            <!-- Book Requests Section -->
            <h2>Book Requests</h2>
            <?php
            if ($request_result->num_rows > 0) {
                while ($row = $request_result->fetch_assoc()) {
                    echo "<p>Book Title: " . $row['book_title'] . " | Requested By: " . $row['email'] . "<br>Details: " . $row['request_details'] . "</p>";

                    // Display replies if any
                    if (isset($replies[$row['id']])) {
                        echo "<h4>Replies:</h4>";
                        foreach ($replies[$row['id']] as $reply) {
                            echo "<p>" . $reply['reply_details'];
                            if ($reply['file_path']) {
                                echo "<div class='book-item'>";

                                echo "  <a href='" . $reply['file_path'] . "' download class='download-btn'>Download</a>";
                                echo "</div>";

                            }
                            echo "</p>";
                        }
                    }
                // Reply form
                echo '<div class="reply-form">';
                echo '<form method="POST" enctype="multipart/form-data">';
                echo '<textarea name="reply_details" placeholder="Provide the book or details..." required></textarea>';
                echo '<input type="file" name="reply_file" required>';
                echo '<input type="hidden" name="reply_request_id" value="' . $row['id'] . '">';
                echo '<br><button type="submit" name="submit_reply">Reply</button>';
                echo '</form>';
                echo '</div>';
            }
        } else {
            echo "<p>No book requests found</p>";
        }
        ?>
    </div>
</body>

</html>