<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Admin Panel</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <meta name="description" content="Secure login panel for admin access">
    <style>
        :root {
            --primary-color: #dc143c;
            --hover-color: rgb(181, 5, 40);
            --bg-color: #f5f5f5;
            --card-color: #ffffff;
            --text-color: #333;
            --border-radius: 10px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--bg-color);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 10px;
        }

        .form-container {
            background: var(--card-color);
            padding: 40px;
            border-radius: var(--border-radius);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: var(--primary-color);
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-color);
            font-weight: bold;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: var(--border-radius);
            font-size: 14px;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: var(--border-radius);
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: var(--primary-color);
            color: #fff;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: var(--hover-color);
        }

        small {
            font-size: 12px;
            color: #555;
        }
    </style>
</head>
<body>
    <form class="form-container" action="auth.php" method="post" autocomplete="on">
        <h2>Login</h2>

        <?php if (isset($_GET['error'])) { ?>
            <div class="error"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php } ?>

        <label for="email">Email</label>
        <input type="email" name="email" id="email" placeholder="Enter your email" autocomplete="email" required aria-describedby="emailHelp">
        <small id="emailHelp" style="display:none;">Your login email.</small>

        <label for="password">Password</label>
        <input type="password" name="password" id="password" placeholder="Enter your password" autocomplete="current-password" required>

        <button type="submit" onclick="this.disabled=true;this.innerText='Logging in...'">Login</button>
    </form>
</body>
</html>
