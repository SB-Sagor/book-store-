<?php
session_start();

$_SESSION['success_msg'] = "Logout successful!";

session_write_close();

header("Location: login.php");
exit();
