<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Open Book</title>
	<link rel="stylesheet" href="style.css">

	<style type="text/css">
		body {
			background: white;
			display: flex;
			justify-content: center;
			align-items: center;
			height: 100vh;
			flex-direction: column;
		}

		* {
			font-family: sans-serif;
			box-sizing: border-box;
		}

		form {
			width: 500px;
			border: 2px solid #ccc;
			padding: 30px;
			background: crimson;
			border-radius: 15px;
		}

		h2 {
			text-align: center;
			margin-bottom: 40px;
		}

		input {
			display: block;
			border: 2px solid #ccc;
			width: 95%;
			padding: 10px;
			margin: 10px auto;
			border-radius: 5px;
		}

		label {
			color: black;
			font-size: 18px;
			padding: 10px;
		}

		button {
			float: right;
			background: black;
			padding: 10px 80px;
			color: #fff;
			border-radius: 5px;
			display: block;
			margin: 0px 123px;
			border: none;
		}

		button:hover {
			opacity: .7;
		}

		.error {
			background: #F2DEDE;
			color: #A94442;
			padding: 10px;
			width: 95%;
			border-radius: 5px;
			margin: 20px auto;
		}

		h1 {
			text-align: center;
			color: #fff;
		}

		a {
			float: right;
			background: red;
			padding: 10px 15px;
			color: black;
			border-radius: 5px;
			margin-right: 10px;
			border: none;
			text-decoration: none;
		}

		a:hover {
			opacity: .7;
		}
	</style>
</head>

<body>
	<form class="form" action="auth.php" method="post">
		<!-- encript the hash password
		<?php
		echo password_hash("123456", PASSWORD_DEFAULT);
		?> -->

		<h2>LOGIN</h2>
		<?php if (isset($_GET['error'])) { ?>
			<p class="error"><?php echo $_GET['error']; ?></p>
		<?php } ?>
		<label>Email</label>
		<input type="email" name="email" placeholder="Enter your email"><br>

		<label>Password</label>
		<input type="password" name="password" placeholder="Enter your password"><br>

		<button type="submit">Login</button>
	</form>
</body>

</html>