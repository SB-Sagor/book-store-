<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Open Book - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link rel="stylesheet" href="style.css">

</head>

<body>
    <nav class="navbar">
        <div class="navdiv">
            <div class="navtext">Open Book</div>
            <ul>
                <li id="allBooksBtn">All Books</li>
                <li id="categoryBtn">Category</li>
                <li id="uploadBtn">Upload</li>
                <li id="requestBtn">Request</li>
                <li></li>
            </ul>
            <button id="accountBtn" class="account-btn">Accounts</button>
        </div>
    </nav>

    <section class="search-section">
        <div class="search-bar">
            <input type="text" placeholder="Search for books, authors, categories...">
            <button type="button">Search</button>
        </div>
    </section>

    <section class="book-section">
        <div class="book-container">

            <div class="book-card">
                <h3>Request</h3>
                <button id="add" class="action-btn">
                    <i class="fas fa-plus"></i> Request for Book
                </button>
            </div>


        </div>
    </section>
    <script>
        document.getElementById("accountBtn").addEventListener("click", () => {
            navigateTo("login.php");
        });

        function navigateTo(page) {
            window.location.href = page;
        }
    </script>
    <script src="script.js"></script>
</body>

</html>