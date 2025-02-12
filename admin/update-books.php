<?php
session_start();
include "db_conn.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch book details
    $query = "SELECT * FROM books WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();

    // Fetch authors
    $authorQuery = "SELECT * FROM authors";
    $authorResult = $conn->query($authorQuery);

    // Fetch categories
    $categoryQuery = "SELECT * FROM category";
    $categoryResult = $conn->query($categoryQuery);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $author_id = $_POST['author_id'];
    $category_id = $_POST['category_id'];
    $description = $_POST['description'];

    // Update book details
    $updateQuery = "UPDATE books SET title=?, author_id=?, category_id=?, description=? WHERE id=?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("siisi", $title, $author_id, $category_id, $description, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Book updated successfully!'); window.location.href='admin.php';</script>";
    } else {
        echo "<script>alert('Error updating book!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
</head>
<body>
    <h2>Edit Book</h2>
    <form method="post">
        <input type="hidden" name="id" value="<?= $book['id']; ?>">

        <label>Title:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($book['title']); ?>" required><br><br>

        <label>Author:</label>
        <select name="author_id" required>
            <?php while ($author = $authorResult->fetch_assoc()): ?>
                <option value="<?= $author['id']; ?>" <?= $book['author_id'] == $author['id'] ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($author['name']); ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <label>Category:</label>
        <select name="category_id" required>
            <?php while ($category = $categoryResult->fetch_assoc()): ?>
                <option value="<?= $category['id']; ?>" <?= $book['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($category['name']); ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <label>Description:</label>
        <textarea name="description" required><?= htmlspecialchars($book['description']); ?></textarea><br><br>

        <button type="submit">Update Book</button>
    </form>
</body>
</html>

<?php $conn->close(); ?>
