<?php
include 'components/connection.php';
session_start();

if (isset($_SESSION['user_id'])) {
	header('location: home.php');
	exit;
}

if (isset($_POST['submit'])) {

	$email = trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
	$pass = $_POST['pass'];

	if (empty($email) || empty($pass)) {
		$warning_msg[] = 'Please fill in all fields.';
	}
	else {
		// Fetch user by email only — never fetch all users
		$select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
		$select_user->execute([$email]);
		$row = $select_user->fetch(PDO::FETCH_ASSOC);

		if ($row && password_verify($pass, $row['password'])) {
			$_SESSION['user_id'] = $row['id'];
			$_SESSION['user_name'] = $row['name'];
			$_SESSION['user_email'] = $row['email'];
			header('location: home.php');
			exit;
		}
		else {
			$warning_msg[] = 'Incorrect email or password.';
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
	<link rel="stylesheet" href="style.css">
	<title>Jimma Coffee Express - Login</title>
</head>

<body>
	<div class="main-container">
		<section class="form-container">
			<div class="title">
				<img src="img/download.png" alt="logo">
				<h1>login now</h1>
			</div>
			<form action="" method="post">
				<div class="input-field">
					<p>your email <sup>*</sup></p>
					<input type="email" name="email" required placeholder="enter your email" maxlength="50"
						oninput="this.value = this.value.replace(/\s/g, '')">
				</div>
				<div class="input-field">
					<p>your password <sup>*</sup></p>
					<input type="password" name="pass" required placeholder="enter your password" maxlength="50"
						oninput="this.value = this.value.replace(/\s/g, '')">
				</div>
				<input type="submit" name="submit" value="login now" class="btn">
				<p>do not have an account? <a href="register.php">register now</a></p>
			</form>
		</section>
	</div>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
	<?php include 'components/alert.php'; ?>
</body>

</html>