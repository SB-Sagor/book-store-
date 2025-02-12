<?php
include "db_conn.php"; // Ensure correct path

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM category WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: admin.php?success=Category deleted successfully!");
    } else {
        header("Location: admin.php?error=Error deleting category!");
    }
    exit;
}
?>
