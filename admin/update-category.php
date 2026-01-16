<?php
session_start();
include "db_conn.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM category WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $category = $result->fetch_assoc();
    } else {
        header("Location: admin.php?error=Category not found");
        exit;
    }
} else {
    header("Location: admin.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_name = trim($_POST['category_name']);
    
    if (!empty($category_name)) {
        $sql = "UPDATE category SET name = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $category_name, $id);
        if ($stmt->execute()) {
            header("Location: admin.php?success=Category updated!");
        } else {
            header("Location: edit-category.php?id=$id&error=Update failed!");
        }
        exit;
    } else {
        header("Location: edit-category.php?id=$id&error=Category name cannot be empty");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Category</title>
</head>

<body>
    <h2>Edit Category</h2>
    <?php if (isset($_GET['error'])): ?>
    <p style="color: red;"><?= $_GET['error']; ?></p>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="category_name" value="<?= htmlspecialchars($category['name']); ?>">
        <button type="submit">Update</button>
    </form>
</body>

</html>z