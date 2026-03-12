<?php
include 'components/connection.php';
session_start();
if (isset($_SESSION['user_id'])) {
	$user_id = $_SESSION['user_id'];
}
else {
	$user_id = '';
}

if (isset($_POST['logout'])) {
	session_destroy();
	header("location: login.php");
	exit;
}

if (isset($_POST['submit-btn'])) {
	$name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
	$email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
	$number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
	$message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);

	$select_message = $conn->prepare("SELECT * FROM `message` WHERE name = ? AND email = ? AND number = ? AND message = ?");
	$select_message->execute([$name, $email, $number, $message]);

	if ($select_message->rowCount() > 0) {
		$warning_msg[] = 'message already sent';
	}
	else {
		$insert_message = $conn->prepare("INSERT INTO `message`(id, user_id, name, email, subject, message) VALUES(?,?,?,?,?,?)");
		$insert_message->execute([unique_id(), $user_id, $name, $email, 'Contact Us', $message]);
		$success_msg[] = 'message sent successfully';
	}
}
?>
<link rel="stylesheet" href="style.css">
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
	<title>Jimma Coffee Express - home page</title>
</head>

<body>
	<?php include 'components/header.php'; ?>
	<div class="main">
		<div class="banner">
			<h1>contact us</h1>
		</div>
		<div class="title2">
			<a href="home.php">home </a><span>/ contact us</span>
		</div>
		<section class="services">
			<div class="box-container">
				<div class="box">
					<img src="img/icon2.png">
					<div class="detail">
						<h3>great savings</h3>
						<p>save big every order</p>
					</div>
				</div>
				<div class="box">
					<img src="img/icon1.png">
					<div class="detail">
						<h3>24*7 support</h3>
						<p>one-on-one support</p>
					</div>
				</div>
				<div class="box">
					<img src="img/icon0.png">
					<div class="detail">
						<h3>gift vouchers</h3>
						<p>vouchers on every festivals</p>
					</div>
				</div>
				<div class="box">
					<img src="img/icon.png">
					<div class="detail">
						<h3>worldwide delivery</h3>
						<p>dropship worldwide</p>
					</div>
				</div>
			</div>
		</section>
		<div class="form-container">
			<form method="post">
				<div class="title">
					<img src="img/download.png" class="logo">
					<h1>leave a message</h1>
				</div>
				<div class="input-field">
					<p>your name <sup>*</sup></p>
					<input type="text" name="name" required>
				</div>
				<div class="input-field">
					<p>your email <sup>*</sup></p>
					<input type="email" name="email" required>
				</div>
				<div class="input-field">
					<p>your number <sup>*</sup></p>
					<input type="text" name="number" required>
				</div>
				<div class="input-field">
					<p>your message <sup>*</sup></p>
					<textarea name="message" required></textarea>
				</div>
				<button type="submit" name="submit-btn" class="btn">send message</button>
			</form>

		</div>
		<div class="address">
			<div class="title">
				<img src="img/download.png" class="logo">
				<h1>contact detail</h1>
				<p>here you can find our contact info's so be sure to check them out </p>
			</div>
			<div class="box-container">
				<div class="box">
					<i class="bx bxs-map-pin"></i>
					<div>
						<h4>address</h4>
						<p>Jimma,JU,JIT</p>
					</div>
				</div>
				<div class="box">
					<i class="bx bxs-phone-call"></i>
					<div>
						<h4>phone number</h4>
						<p>09********</p>
					</div>
				</div>
				<div class="box">
					<i class="bx bxs-map-pin"></i>
					<div>
						<h4>email</h4>
						<p>section2group1@gmail.com</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
	<script src="script.js"></script>
	<?php include 'components/alert.php'; ?>
</body>

</html>