<?php
session_start();
include "db_conn.php";

function sanitize($data)
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function redirectWithError($msg)
{
    header("Location: login.php?error=" . urlencode($msg));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = sanitize($_POST['email'] ?? '');
    $password = sanitize($_POST['password'] ?? '');

    if (empty($email)) {
        redirectWithError("Email is required");
    }

    if (empty($password)) {
        redirectWithError("Password is required");
    }

    $stmt = $conn->prepare("SELECT email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            session_regenerate_id(true); // Prevent session fixation
            $_SESSION['email'] = $user['email'];
            header("Location: admin.php");
            exit();
        } else {
            redirectWithError("Incorrect Email or password");
        }
    } else {
        redirectWithError("Incorrect Email or password");
    }

    $stmt->close();
    $conn->close();
} else {
    redirectWithError("Invalid request");
}
