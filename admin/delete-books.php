<?php
session_start();
include "db_conn.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the book details to get the file paths
    $query = "SELECT cover, file FROM books WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc();

        // Get the file paths for cover and book file
        $coverPath = $book['cover'];
        $filePath = $book['file'];

        // Delete the book record from the database
        $deleteQuery = "DELETE FROM books WHERE id = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param("i", $id);

        if ($deleteStmt->execute()) {
            // Check if the cover and book files exist and delete them
            if (file_exists($coverPath)) {
                unlink($coverPath);  // Delete cover image
            }
            if (file_exists($filePath)) {
                unlink($filePath);  // Delete book file
            }

            // Redirect with success message
            header("Location: admin.php?success=Book deleted successfully!");
        } else {
            header("Location: admin.php?error=Error deleting book!");
        }
    } else {
        header("Location: admin.php?error=Book not found!");
    }
} else {
    echo "<script>alert('Invalid request!'); window.location.href='admin.php';</script>";
}

$conn->close();
