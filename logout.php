<?php
session_start();

// Set a logout message
$_SESSION['logout_message'] = "You have been logged out successfully.";

// Clear all session data
session_unset();
session_destroy();

// Redirect to homepage or login page
header("Location: index.php");
$_SESSION['logout_message'] = "You have been logged out successfully.";

exit();
?>
