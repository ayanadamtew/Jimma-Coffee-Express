<?php
include '../components/connection.php';
session_start();

if (isset($_POST['submit'])) {

	$name = trim(filter_var($_POST['name'], FILTER_SANITIZE_STRING));
	$pass = $_POST['password'];

	$select_admin = $conn->prepare("SELECT * FROM `admin` WHERE name = ?");
	$select_admin->execute([$name]);
	$row = $select_admin->fetch(PDO::FETCH_ASSOC);

	if ($row && password_verify($pass, $row['password'])) {
		$_SESSION['admin_id'] = $row['id'];
		$_SESSION['admin_name'] = $row['name'];
		header('location: dashboard.php');
		exit;
	}
	else {
		$message[] = 'Incorrect username or password.';
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
	<link rel="stylesheet" href="admin_style.css">
	<title>Admin Login - Jimma Coffee Express</title>
</head>

<body style="padding-left: 0 !important;">
	<?php
if (isset($message)) {
	foreach ($message as $msg) {
		echo '
            <div class="message">
                <span>' . htmlspecialchars($msg) . '</span>
                <i class="bx bx-x" onclick="this.parentElement.remove();"></i>
            </div>';
	}
}
?>
	<div class="main-container">
		<section class="form-container" id="admin_login">
			<form action="" method="post">
				<h3>Admin Login</h3>
				<div class="input-field">
					<label>Username <sup>*</sup></label><br>
					<input type="text" name="name" maxlength="50" required placeholder="Enter your username">
				</div>
				<div class="input-field">
					<label>Password <sup>*</sup></label><br>
					<input type="password" name="password" maxlength="50" required placeholder="Enter your password">
				</div>
				<input type="submit" name="submit" value="login now" class="btn">
			</form>
		</section>
	</div>
</body>

</html>