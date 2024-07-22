<?php 
	include 'components/connection.php';
	session_start();

	if (isset($_SESSION['user_id'])) {
		$user_id = $_SESSION['user_id'];
	}else{
		$user_id = '';
	}

	//register user
	if (isset($_POST['submit'])) {
		$id = unique_id();
		$name = $_POST['name'];
		$name = filter_var($name, FILTER_SANITIZE_STRING);
		$email = $_POST['email'];
		$email = filter_var($email, FILTER_SANITIZE_STRING);
		$pass = $_POST['pass'];
		$pass = filter_var($pass, FILTER_SANITIZE_STRING);
		$cpass = $_POST['cpass'];
		$cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

		$select_user = $conn->prepare("SELECT * FROM `users` WHERE  email = ?");
		$select_user->execute([$email]);
		$row = $select_user->fetch(PDO::FETCH_ASSOC);

		if ($select_user->rowCount() > 0) {
			$warning_msg[] = 'email already exist';
		}else{
			if (!preg_match("/^[a-zA-Z ]+$/", $name)) {
				$warning_msg[] = 'Invalid name';
			}
			else if(!preg_match("/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/", $email)){
				$warning_msg[] = 'Invalid Email';
			}
			else if($pass != $cpass){
				$warning_msg[] = 'confirm your password';
				
			}
			else{
				$insert_user = $conn->prepare("INSERT INTO `users`(id,name,email,password) VALUES(?,?,?,?)");
				$insert_user->execute([$id,$name,$email,$pass]);
				header('location: home.php');
				$select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ?");
				$select_user->execute([$email, $pass]);
				$row = $select_user->fetch(PDO::FETCH_ASSOC);
				if ($select_user->rowCount() > 0) {
					$_SESSION['user_id'] = $row['id'];
					$_SESSION['user_name'] = $row['name'];
					$_SESSION['user_email'] = $row['email'];
				}
			}
		}
	}

?>
<style type="text/css">
	<?php include 'style.css'; ?>
</style>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>green tea - register now</title>
</head>
<body>
	<div class="main-container">
		<section class="form-container">
			<div class="title">
				<img src="img/download.png">
				<h1>register now</h1>
				
			</div>
			<form action="" method="post">
				<div class="input-field">
					<p>your name <sup>*</sup></p>
					<input type="text" id="name" onblur="validateName()" name="name" required placeholder="enter your name" maxlength="50">
					<p id="name_err"></p>
				</div>
				<div class="input-field">
					<p>your email <sup>*</sup></p>
					<input type="email" id="email" onblur="validateEmail()" name="email" required placeholder="enter your email" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
					<p id="email_err"></p>
				</div>
				<div class="input-field">
					<p>your passwod <sup>*</sup></p>
					<input type="password" id="password" onblur="validatepass()" name="pass" required placeholder="enter your passwod" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
					<p id="pass_err"></p>
				</div>
				<div class="input-field">
					<p>confirm passwod <sup>*</sup></p>
					<input type="password" id="confirmPassword" onblur="validateConfirmPassword()" name="cpass" required placeholder="enter your passwod" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
					<p id="confirm_err"></p>
				</div>
				<input type="submit" name="submit" value="register now" class="btn">
				<p>already have an account? <a href="login.php">login now</a></p>
			</form>
		</section>
	</div>
	<script>

    function validateEmail() {
      var email = document.getElementById('email').value
      var regex = /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/
      if (!regex.test(email)) {
        document.getElementById('email_err').innerHTML = "invalid email"
		document.getElementById('email_err').style.color = "red";

      }
	  else{
		document.getElementById('email_err').innerHTML = "valid email"
		document.getElementById('email_err').style.color = "green";
	  }
    }

    function validateName() {
      var name = document.getElementById('name').value
      var regex = /^[A-Za-z]+$/
      if (!regex.test(name)) {
        document.getElementById("name_err").innerHTML = "Invalid name"
		document.getElementById('name_err').style.color = "red";
      }
	  else{
		document.getElementById('name_err').innerHTML = "valid name"
		document.getElementById('name_err').style.color = "green";
	  }
	}
	function validatepass(){
		var pass=document.getElementById("password").value;
		if (pass.length < 6) {
			document.getElementById('pass_err').innerHTML ="Password must be at least 6 characters long.";
			document.getElementById('pass_err').style.color = "red";
    }
	else{
		document.getElementById('pass_err').innerHTML ="Valid password.";
			document.getElementById('pass_err').style.color = "green";

	}
	}
	  function validateConfirmPassword() {
  		var password = document.getElementById("password").value;
 		var confirmPassword = document.getElementById("confirmPassword").value;

 		if (password !== confirmPassword) {
    		document.getElementById("confirm_err").innerHTML ="Passwords do not match";
			document.getElementById('confirm_err').style.color = "red";
  			} else {
    		document.getElementById("confirm_err").innerHTML ="Passwords match";
			document.getElementById('confirm_err').style.color = "green";
  		}

    }
  </script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
	<?php include 'components/alert.php'; ?>
</body>
</html>