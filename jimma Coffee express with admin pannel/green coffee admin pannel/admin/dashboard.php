<?php 
	 include '../components/connection.php';
	 session_start();

	 $admin_id = $_SESSION['admin_id'];

	 if (!isset($admin_id)) {
	 	header('location: admin_login.php');
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
	<title>admin dashboard</title>
</head>
<body>
	<?php include '../components/admin_header.php'; ?>
	<div class="main">
		<div class="banner">
			<h1>dashboard</h1>
		</div>
		<div class="title2">
			<a href="home.php">home </a><span>/ dashboard</span>
		</div>
		<section class="dashboard">
			<h1 class="heading">dashboard</h1>
			<div class="box-container">
				<div class="box">
					<h3>welcome!</h3>
					<p><?=$fetch_profile['name']; ?></p>
					<a href="update_profile.php" class="btn">update profile</a>
				</div>
				
				
				<div class="box">
					<?php 
						$select_post = $conn->prepare("SELECT * FROM `products`");
						$select_post->execute();
						$number_of_posts = $select_post->rowCount();
					?>
					<h3><?= $number_of_posts; ?></h3>
					<p>products added</p>
					<a href="add_posts.php" class="btn">add new post</a>
				</div>
				<div class="box">
					<?php 
						$select_active_post = $conn->prepare("SELECT * FROM `products` WHERE status = ?");
						$select_active_post->execute(['active']);
						$number_of_active_post = $select_active_post->rowCount();
					?>
					<h3><?= $number_of_active_post; ?></h3>
					<p>active posts</p>
					<a href="view_posts.php" class="btn">see posts</a>
				</div>
				
				<div class="box">
					<?php 
						$select_deactive_post = $conn->prepare("SELECT * FROM `products` WHERE status = ?");
						$select_deactive_post->execute(['deactive']);
						$number_of_deactive_post = $select_deactive_post->rowCount();
					?>
					<h3><?= $number_of_deactive_post; ?></h3>
					<p>deactive posts</p>
					<a href="view_posts.php" class="btn">see posts</a>
				</div>
				<div class="box">
					<?php 
						$select_users = $conn->prepare("SELECT * FROM `users`");
						$select_users->execute();
						$number_of_users = $select_users->rowCount();
					?>
					<h3><?= $number_of_users; ?></h3>
					<p>users account</p>
					<a href="user_accounts.php" class="btn">see users</a>
				</div>
				<div class="box">
					<?php 
						$select_admins = $conn->prepare("SELECT * FROM `admin`");
						$select_admins->execute();
						$number_of_admins = $select_admins->rowCount();
					?>
					<h3><?= $number_of_admins; ?></h3>
					<p>admins account</p>
					<a href="admin_accounts.php" class="btn">see admin</a>
				</div>
				<div class="box">
					<?php
			         $select_comments = $conn->prepare("SELECT * FROM `message`");
			         $select_comments->execute();
			         $select_comments->execute();
			         $numbers_of_comments = $select_comments->rowCount();
			      ?>
			      <h3><?= $numbers_of_comments; ?></h3>
			      <p>messages added</p>
			      <a href="admin_message.php" class="btn">see messages</a>
				</div>
			   <div class="box">
			      <?php
			         $select_canceled_order = $conn->prepare("SELECT * FROM `orders` WHERE status = ?");
			         $select_canceled_order->execute(['canceled']);
			         $total_canceled_order = $select_canceled_order->rowCount();
			      ?>
			      <h3><?= $total_canceled_order; ?></h3>
			      <p>total canceled order</p>
			      <a href="admin_order.php" class="btn">see orders</a>
			   </div>
			   <div class="box">
			      <?php
			         $select_confirm_order = $conn->prepare("SELECT * FROM `orders` WHERE status = ?");
			         $select_confirm_order->execute(['in progress']);
			         $total_confirm_order = $select_confirm_order->rowCount();
			      ?>
			      <h3><?= $total_confirm_order; ?></h3>
			      <p>total order in progress</p>
			      <a href="admin_order.php" class="btn">see orders</a>
			   </div>
			   <div class="box">
			      <?php
			         $select_total_order = $conn->prepare("SELECT * FROM `orders`");
			         $select_total_order->execute();
			         $total_total_order = $select_total_order->rowCount();
			      ?>
			      <h3><?= $total_total_order; ?></h3>
			      <p>total order placed</p>
			      <a href="admin_order.php" class="btn">see orders</a>
			   </div>
			</div>

		</section>
	</div>
	
	<script src="script.js"></script>
</body>
</html>