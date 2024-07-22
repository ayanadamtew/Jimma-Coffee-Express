<?php
include '../components/connection.php';
session_start();
if (isset($_POST['submit'])) {

	$name = $_POST['name'];
	$name = filter_var($name, FILTER_SANITIZE_STRING);

	$pass = sha1($_POST['password']);
	$pass = filter_var($pass, FILTER_SANITIZE_STRING);

	$select_admin = $conn->query("SELECT * FROM `admin` ");
	if ($select_admin->rowCount() > 0) {
		$fetch_admin_id = $select_admin->fetch(PDO::FETCH_ASSOC);
		$_SESSION['admin_id'] = $fetch_admin_id['id'];
		header('location:dashboard.php');
	} else {
		$message[] = 'incorrect username or password';
	}
}
?>
<style>
	<?php include 'admin_style.css'; ?>
</style>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- font awesome cdn link  -->
	<link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
	<title>Admin Login page</title>
</head>

<body style="padding-left: 0 !important;">
	<?php
	if (isset($message)) {
		foreach ($message as $message) {
			echo '
					<div class="message">
						<span>' . $message . '</span>
						<i class="bx bx-x" onclick="this.parentElement.remove();"></i>
					</div>
				';
		}
	}
	?>
	<div class="main-container">
		<section class="form-container" id="admin_login">
			<form action="" method="post">
				<h3>login now</h3>
				<p style="text-align: center; color: #000;">Default user name : admin and password : 4321</p>
				<div class="input-field">
					<label>User name <sup>*</sup></label><br>
					<input type="text" name="name" maxlength="20" required placeholder="Enter your username" oninput="this.value.replace(/\s/g,'')">
				</div>
				<div class="input-field">
					<label>password <sup>*</sup></label><br>
					<input type="password" name="password" maxlength="20" required placeholder="Enter your password" oninput="this.value.replace(/\s/g,'')">
				</div>
				<input type="submit" name="submit" value="login now" class="btn">
			</form>
		</section>
	</div>
</body>

</html>