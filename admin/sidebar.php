<div class="navbar">
    <span class="hamburger" onclick="toggleSidebar()">☰</span>
    📚 Open Book — Admin Panel
</div>

<aside class="sidebar" id="sidebar">
    <ul>
        <li onclick="navigateTo('/book-store-/admin/authors/add-author.php')">✍️ Add Author</li>
        <li onclick="navigateTo('/book-store-/admin/categories/add-category.php')">📂 Add Category</li>
        <li onclick="navigateTo('/book-store-/admin/books/add-books.php')">📖 Add Books</li>
        <li onclick="navigateTo('/book-store-/books.php')">📚 All Books</li>
        <li onclick="navigateTo('/book-store-/index.php')">📨 Book Requests</li>
        <li onclick="navigateTo('/book-store-/admin/admin.php')">👤 Manage Users</li>
        <li onclick="navigateTo('#')">⚙️ Settings</li>
        <li onclick="navigateTo('/book-store-/admin/logout.php')">🚪 Logout</li>

    </ul>
</aside>