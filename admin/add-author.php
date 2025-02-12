
<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "authentication");


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["author_name"])) {
    $author_name = trim($_POST["author_name"]);

    if (!empty($author_name)) {
        $stmt = $conn->prepare("INSERT INTO authors (name) VALUES (?)");
        $stmt->bind_param("s", $author_name);
        if ($stmt->execute()) {
            echo "<script>alert('Author added successfully!'); window.location.href='admin.php';</script>";
        } else {
            echo "<script>alert('Error adding author!');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Author name cannot be empty!');</script>";
    }
}
$conn->close();
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
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        .navbar {
            background-color: crimson;
            color: white;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navtext {
            cursor: pointer;

            font-size: 1rem;
            font-weight: bold;
        }
        
        .main-container {
            display: flex;
            flex: 1;
        }
        .admin {
            width: 20%;
            background-color: crimson;
            padding: 1rem;
            color: white;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .admin h3 {
            text-align: center;
        }
        .admin-list {
            list-style: none;
            padding: 0;
            margin: 0;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .admin-list li {
            cursor: pointer;
            padding: 10px;
            background-color: rgb(200, 10, 50);
            color: white;
            text-align: center;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .admin-list li:hover {
            background-color: rgb(190, 8, 44);
        }
        .content {
            flex: 1;
            padding: 1rem;
            background-color: white;
            border-radius: 5px;
            margin: 2px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .content h1 {
            text-align: center;
            margin-bottom: 1.5rem;
                    }
        
        
        .form-container {
    background-color: crimson;
    padding: 2rem;

    border-radius: 0.5rem;
/*    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);*/
            height: 50vh;
            width: 50vw;
            padding: 40px 60px;
            margin:30px 100px;
            
}

.form-title {
    text-align: center;
    padding-bottom: 1.5rem;
    font-size: 1.75rem;
    color: black;
}

.form-group {
    margin-bottom: 2rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: white;
}

.form-group input {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ccc;
    border-radius: 0.25rem;
}

.btn {
            width: 100%;

            padding: 10px ;
            background-color: black;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
}

.btn:hover {
            background-color: #292323;
}
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navtext">Open Book</div>
        
    </nav>
    
    <div class="main-container">
        <section class="admin">
            <h3 id="backadmin">ADMIN PANEL</h3>
            <ul class="admin-list">
                <li id="allBooks">View Books</li>
                <li id="addAuthor">Add Author</li>
                <li id="addBooks">Add Books</li>
                <li id="addCategory">Add Category</li>
                <li id="requests">Book Requests</li>
                <li id="manageUsers">Manage Users</li>
                <li id="settings">Settings</li>
                <li id="logout">Logout</li>
            </ul>
        </section>
        
        <section class="content">
            
     <form action="add-author.php" method="post" class="form-container">
            <h1 class="form-title">Add New Author</h1>
            <!-- PHP error and success messages removed -->
            <div class="form-group">
                <label for="author_name">Author Name</label>
                <input type="text" id="author_name" name="author_name" class="form-control">
            </div>
            <button type="submit" class="btn">Add Author</button>
        </form>
        </section>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelector(".navtext").addEventListener("click", () => {
    navigateTo("books.php");
  });
            document.getElementById("backadmin").addEventListener("click", () => navigateTo("admin.php"));
            document.getElementById("addAuthor").addEventListener("click", () => navigateTo("add-author.php"));
            document.getElementById("allBooks").addEventListener("click", () => navigateTo("books.php"));
            document.getElementById("addBooks").addEventListener("click", () => navigateTo("add-books.php"));
            document.getElementById("addCategory").addEventListener("click", () => navigateTo("add-category.php"));
            document.getElementById("manageUsers").addEventListener("click", () => navigateTo("manage-users.php"));
            document.getElementById("settings").addEventListener("click", () => navigateTo("settings.php"));
            document.getElementById("logout").addEventListener("click", () => navigateTo("logout.php"));
        });
        function navigateTo(page) {
            window.location.href = page;
        }
    </script>
</body>
</html>
