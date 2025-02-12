<?php
session_start();
include "db_conn.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete book
    $query = "DELETE FROM books WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: admin.php?success=Book deleted successfully!");
    } else {
        header("Location: admin.php?error=Error deleting book!");
    }
} else {
    echo "<script>alert('Invalid request!'); window.location.href='admin.php';</script>";
}

$conn->close();
?>
