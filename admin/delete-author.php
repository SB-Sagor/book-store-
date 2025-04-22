<?php
session_start();
include "db_conn.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $sql = "DELETE FROM authors WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: admin.php?success=Author deleted!");
    } else {
        header("Location: admin.php?error=Deletion failed!");
    }
    exit;
} else {
    header("Location: admin.php");
    exit;
}
?>
