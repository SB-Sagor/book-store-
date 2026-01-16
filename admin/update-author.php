<?php
session_start();
include "db_conn.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM authors WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $author = $result->fetch_assoc();
    } else {
        header("Location: admin.php?error=Author not found");
        exit;
    }
} else {
    header("Location: admin.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['author_name'];

    if (!empty($name)) {
        $sql = "UPDATE authors SET name = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $name, $id);
        if ($stmt->execute()) {
            header("Location: admin.php?success=Author updated!");
        } else {
            header("Location: admin.php?error=Update failed!");
        }
        exit;
    } else {
        header("Location: update-author.php?id=$id&error=Name cannot be empty");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Author</title>
</head>

<body>
    <h2>Edit Author</h2>
    <form method="POST">
        <input type="text" name="author_name" value="<?= htmlspecialchars($author['name']); ?>">
        <button type="submit">Update</button>
    </form>
</body>

</html>