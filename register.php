<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);
    if ($stmt->execute()) {
        echo "Registration successful!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
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
        padding: 20px;
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

    .login-link {
        margin-top: 10px;
        display: inline-block;
        color: #007bff;
        text-decoration: none;
        font-size: 14px;
    }

    .login-link:hover {
        text-decoration: underline;
    }
</style> -->
</head>
<body>
    <div class="form-container">
    <h2>User Register</h2>
    <form method="POST">
    <input type="text" name="name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Register</button>
    <a class="login-link" href="login.php">Already have an account? Login here</a>
</form>
    </div>
    
</body>
</html>



