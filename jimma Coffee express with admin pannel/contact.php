<?php
// contact.php
// *** IMPORTANT: Call session_start() at the very top of the file ***
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'components/connection.php'; 

$user_id = $_SESSION['user_id'] ?? '';

// Logout logic (consider moving this to a dedicated logout.php for cleaner separation)
if (isset($_POST['logout'])) {
    session_destroy();
    header("location: login.php");
    exit;
}

// Fetch user data for pre-filling form if logged in
$user_name = '';
$user_email = '';
if (!empty($user_id)) {
    try {
        $select_user = $conn->prepare("SELECT name, email FROM `users` WHERE id = ? LIMIT 1");
        $select_user->execute([$user_id]);
        if ($select_user->rowCount() > 0) {
            $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);
            $user_name = $fetch_user['name'];
            $user_email = $fetch_user['email'];
        }
    } catch (PDOException $e) {
        error_log("Error fetching user profile for contact form: " . $e->getMessage());
        // Optionally set a general error message for the user if profile fetch fails
        // $_SESSION['error_msg'] = 'Could not retrieve your profile information.';
    }
}


// ✅ Handle form submission
if (isset($_POST['submit-btn'])) {
    // Sanitize and validate inputs
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
    $subject = filter_var($_POST['subject'], FILTER_SANITIZE_STRING);
    $message_content = filter_var($_POST['message'], FILTER_SANITIZE_STRING); // Renamed to avoid conflicts

    // Determine user_id to insert (NULL if not logged in)
    $insert_user_id = !empty($user_id) ? $user_id : NULL;

    try {
        $insert_msg = $conn->prepare("INSERT INTO `message` (user_id, name, email, number, subject, message) VALUES (?, ?, ?, ?, ?, ?)");
        $insert_msg->execute([$insert_user_id, $name, $email, $number, $subject, $message_content]);

        // Set a success message in session for SweetAlert
        $_SESSION['success_msg'] = 'Your message has been sent successfully! We will get back to you soon.';

    } catch (PDOException $e) {
        // Log the error for developers (check your server's PHP error logs)
        error_log("Contact form submission error: " . $e->getMessage());

        // Set an error message in session for SweetAlert
        // Avoid exposing raw database errors to the user
        $_SESSION['error_msg'] = 'Failed to send message. Please try again later.';
    }

    // *** IMPORTANT: Redirect to prevent form re-submission on refresh (Post/Redirect/Get pattern) ***
    header('location: contact.php');
    exit; // Always exit after a header redirect
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
	<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
	<title>Jimma Coffee Express - Contact Us</title>
</head>

<body>
	<?php include 'components/header.php'; // Include your user header ?>

	<div class="main">
		<div class="banner">
			<h1>contact us</h1>
		</div>
		<div class="title2">
			<a href="index.php">home </a><span>/ contact us</span>
		</div>

		<section class="services">
			<div class="box-container">
				<div class="box">
					<img src="img/icon2.png" alt="Great Savings">
					<div class="detail">
						<h3>great savings</h3>
						<p>save big every order</p>
					</div>
				</div>
				<div class="box">
					<img src="img/icon1.png" alt="24/7 Support">
					<div class="detail">
						<h3>24*7 support</h3>
						<p>one-on-one support</p>
					</div>
				</div>
				<div class="box">
					<img src="img/icon0.png" alt="Gift Vouchers">
					<div class="detail">
						<h3>gift vouchers</h3>
						<p>vouchers on every festivals</p>
					</div>
				</div>
				<div class="box">
					<img src="img/icon.png" alt="Worldwide Delivery">
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
					<img src="img/download.png" class="logo" alt="Logo">
					<h1>leave a message</h1>
				</div>
				<div class="input-field">
					<p>your name <sup>*</sup></p>
					<input type="text" name="name" required value="<?= htmlspecialchars($user_name); ?>">
				</div>
				<div class="input-field">
					<p>your email <sup>*</sup></p>
					<input type="email" name="email" required value="<?= htmlspecialchars($user_email); ?>">
				</div>
				<div class="input-field">
					<p>your number <sup>*</sup></p>
					<input type="text" name="number" required>
				</div>
				<div class="input-field">
					<p>subject <sup>*</sup></p>
					<input type="text" name="subject" required>
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
				<img src="img/download.png" class="logo" alt="Logo">
				<h1>contact detail</h1>
				<p>here you can find our contact info's so be sure to check them out</p>
			</div>
			<div class="box-container">
				<div class="box">
					<i class="bx bxs-map-pin"></i>
					<div>
						<h4>address</h4>
						<p>Holeta, Oromia, Ethiopia</p>
					</div>
				</div>
				<div class="box">
					<i class="bx bxs-phone-call"></i>
					<div>
						<h4>phone number</h4>
						<p>0973395537</p>
					</div>
				</div>
				<div class="box">
					<i class="bx bxs-envelope"></i>
					<div>
						<h4>email</h4>
						<p>ayanadamtew@gmail.com</p>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php include 'components/footer.php'; // Include your user footer ?>

	<!-- SweetAlert CDN - Must be before components/alert.php -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
	<script src="script.js"></script>
	<?php include 'components/alert.php'; // This will display the SweetAlerts ?>

</body>

</html>