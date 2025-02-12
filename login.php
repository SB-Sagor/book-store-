<?php
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashed_password);
    $stmt->fetch();

    if (password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $id;
        header("Location: index.php");
    } else {
        echo "Invalid login!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <!-- <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        /* display: flex; */
        justify-content: center;
        align-items: center;
        height: 100vh;
        width: 40vw;
        padding: 0px 25rem;
        margin: 0;
    }
    
    .form-container {
        background: white;
        padding: 60px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        width: 350px;
        text-align: center;
    }
    
    h2 {
        margin-bottom: 20px;
        color: #333;
    }

    input {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
    }

    button {
        width: 100%;
        padding: 10px;
        background:crimson;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: 0.3s;
    }

    button:hover {
        background:rgb(255, 145, 0);
    }

    .register-link {
        margin-top: 10px;
        display: inline-block;
        color: #007bff;
        text-decoration: none;
        font-size: 14px;
    }

    .register-link:hover {
        text-decoration: underline;
    }
</style> -->
</head>
<body>
    <div class="form-container">
    <h2>User Login</h2>

    <form method="POST">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
    <a class="register-link" href="register.php">Don't have an account? Register here</a>
</form>
    </div>
</body>
</html>

