<?php
include 'components/connection.php';
session_start();

if (isset($_SESSION['user_id'])) {
	header('location: home.php');
	exit;
}

if (isset($_POST['submit'])) {

	$id = unique_id();
	$name = trim(filter_var($_POST['name'], FILTER_SANITIZE_STRING));
	$email = trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
	$pass = $_POST['pass'];
	$cpass = $_POST['cpass'];

	// Validate name
	if (!preg_match("/^[a-zA-Z ]+$/", $name)) {
		$warning_msg[] = 'Invalid name — only letters and spaces allowed.';
	}
	// Validate email
	elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$warning_msg[] = 'Invalid email address.';
	}
	// Validate password length
	elseif (strlen($pass) < 6) {
		$warning_msg[] = 'Password must be at least 6 characters.';
	}
	// Confirm password match
	elseif ($pass !== $cpass) {
		$warning_msg[] = 'Passwords do not match.';
	}
	else {
		// Check if email already exists
		$check = $conn->prepare("SELECT id FROM `users` WHERE email = ?");
		$check->execute([$email]);

		if ($check->rowCount() > 0) {
			$warning_msg[] = 'An account with this email already exists.';
		}
		else {
			// Hash password securely
			$hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

			$insert = $conn->prepare("INSERT INTO `users`(id, name, email, password) VALUES(?, ?, ?, ?)");
			$insert->execute([$id, $name, $email, $hashed_pass]);

			// Auto-login after registration
			$_SESSION['user_id'] = $id;
			$_SESSION['user_name'] = $name;
			$_SESSION['user_email'] = $email;

			$success_msg[] = 'Account created! Welcome, ' . htmlspecialchars($name) . '.';
			header('location: home.php');
			exit;
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="style.css">
	<title>Jimma Coffee Express - Register</title>
</head>

<body>
	<div class="main-container">
		<section class="form-container">
			<div class="title">
				<img src="img/download.png" alt="logo">
				<h1>register now</h1>
			</div>
			<form action="" method="post">
				<div class="input-field">
					<p>your name <sup>*</sup></p>
					<input type="text" id="name" onblur="validateName()" name="name" required
						placeholder="enter your name" maxlength="50">
					<p id="name_err"></p>
				</div>
				<div class="input-field">
					<p>your email <sup>*</sup></p>
					<input type="email" id="email" onblur="validateEmail()" name="email" required
						placeholder="enter your email" maxlength="50"
						oninput="this.value = this.value.replace(/\s/g, '')">
					<p id="email_err"></p>
				</div>
				<div class="input-field">
					<p>your password <sup>*</sup></p>
					<input type="password" id="password" onblur="validatepass()" name="pass" required
						placeholder="enter your password" maxlength="50"
						oninput="this.value = this.value.replace(/\s/g, '')">
					<p id="pass_err"></p>
				</div>
				<div class="input-field">
					<p>confirm password <sup>*</sup></p>
					<input type="password" id="confirmPassword" onblur="validateConfirmPassword()" name="cpass" required
						placeholder="confirm your password" maxlength="50"
						oninput="this.value = this.value.replace(/\s/g, '')">
					<p id="confirm_err"></p>
				</div>
				<input type="submit" name="submit" value="register now" class="btn">
				<p>already have an account? <a href="login.php">login now</a></p>
			</form>
		</section>
	</div>
	<script>
		function validateEmail() {
			var email = document.getElementById('email').value;
			var regex = /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;
			var el = document.getElementById('email_err');
			if (!regex.test(email)) {
				el.innerHTML = "invalid email";
				el.style.color = "red";
			} else {
				el.innerHTML = "valid email";
				el.style.color = "green";
			}
		}
		function validateName() {
			var name = document.getElementById('name').value;
			var regex = /^[A-Za-z ]+$/;
			var el = document.getElementById("name_err");
			if (!regex.test(name)) {
				el.innerHTML = "Invalid name";
				el.style.color = "red";
			} else {
				el.innerHTML = "valid name";
				el.style.color = "green";
			}
		}
		function validatepass() {
			var pass = document.getElementById("password").value;
			var el = document.getElementById('pass_err');
			if (pass.length < 6) {
				el.innerHTML = "Password must be at least 6 characters.";
				el.style.color = "red";
			} else {
				el.innerHTML = "Valid password.";
				el.style.color = "green";
			}
		}
		function validateConfirmPassword() {
			var password = document.getElementById("password").value;
			var confirmPassword = document.getElementById("confirmPassword").value;
			var el = document.getElementById("confirm_err");
			if (password !== confirmPassword) {
				el.innerHTML = "Passwords do not match";
				el.style.color = "red";
			} else {
				el.innerHTML = "Passwords match";
				el.style.color = "green";
			}
		}
	</script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
	<?php include 'components/alert.php'; ?>
</body>

</html>