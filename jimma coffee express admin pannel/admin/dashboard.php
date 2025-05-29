<?php
include '../components/connection.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
	header('location: admin_login.php');
}

// Fetch admin profile for the welcome message
// It's good practice to ensure $fetch_profile is set
$fetch_profile = []; // Initialize to prevent errors if not found
try {
    $select_profile = $conn->prepare("SELECT * FROM `admin` WHERE id = ? LIMIT 1");
    $select_profile->execute([$admin_id]);
    if ($select_profile->rowCount() > 0) {
        $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    // Handle error, e.g., log it or show a message
    // echo "Error fetching profile: " . $e->getMessage();
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
	<!-- font awesome cdn link - Keeping as it was -->
	<link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
	<title>Admin Dashboard</title>
</head>

<body>
	<?php include '../components/admin_header.php'; ?>
	<div class="main">
		<div class="banner">
			<h1>Dashboard</h1>
		</div>
		<div class="title2">
			<a href="home.php">Home </a><span>/ Dashboard</span>
		</div>
		<section class="dashboard">
			<h1 class="heading">Dashboard Overview</h1>
			<div class="box-container">
				<!-- Welcome Box - Added a class for potential future styling -->
				<div class="box welcome-box">
					<i class='bx bxs-user-detail'></i> <!-- Icon for profile -->
					<h3>Welcome!</h3>
					<p><?= htmlspecialchars($fetch_profile['name'] ?? 'Admin'); ?></p>
					<a href="update_profile.php" class="btn">Update Your Profile</a>
				</div>

				<!-- Products Added Box -->
				<div class="box">
					<?php
					$select_post = $conn->prepare("SELECT * FROM `product`");
					$select_post->execute();
					$number_of_posts = $select_post->rowCount();
					?>
					<i class='bx bxs-package'></i> <!-- Icon for products -->
					<h3><?= $number_of_posts; ?></h3>
					<p>Products Added</p>
					<a href="add_posts.php" class="btn">Add New Product</a>
				</div>

				<!-- Active Products Box -->
				<div class="box">
					<?php
					$select_active_post = $conn->prepare("SELECT * FROM product WHERE status = ?");
					$select_active_post->execute(['active']);
					$number_of_active_post = $select_active_post->rowCount();
					?>
					<i class='bx bxs-check-circle'></i> <!-- Icon for active status -->
					<h3><?= $number_of_active_post; ?></h3>
					<p>Active Products</p>
					<a href="view_posts.php" class="btn">View Active Products</a>
				</div>

				<!-- Deactive Products Box -->
				<div class="box">
					<?php
					$select_deactive_post = $conn->prepare("SELECT * FROM `product` WHERE status = ?");
					$select_deactive_post->execute(['deactive']);
					$number_of_deactive_post = $select_deactive_post->rowCount();
					?>
					<i class='bx bxs-x-circle'></i> <!-- Icon for inactive status -->
					<h3><?= $number_of_deactive_post; ?></h3>
					<p>Inactive Products</p>
					<a href="view_posts.php" class="btn">View Inactive Products</a>
				</div>

				<!-- User Accounts Box -->
				<div class="box">
					<?php
					$select_users = $conn->prepare("SELECT * FROM `users`");
					$select_users->execute();
					$number_of_users = $select_users->rowCount();
					?>
					<i class='bx bxs-group'></i> <!-- Icon for users -->
					<h3><?= $number_of_users; ?></h3>
					<p>User Accounts</p>
					<a href="user_accounts.php" class="btn">Manage Users</a>
				</div>

				<!-- Admin Accounts Box -->
				<div class="box">
					<?php
					$select_admins = $conn->prepare("SELECT * FROM `admin`");
					$select_admins->execute();
					$number_of_admins = $select_admins->rowCount();
					?>
					<i class='bx bxs-user-shield'></i> <!-- Icon for admins -->
					<h3><?= $number_of_admins; ?></h3>
					<p>Admin Accounts</p>
					<a href="admin_accounts.php" class="btn">Manage Admins</a>
				</div>

				<!-- Messages Box -->
				<div class="box">
					<?php
					$select_comments = $conn->prepare("SELECT * FROM `message`");
					$select_comments->execute(); // Corrected: removed duplicate execute()
					$numbers_of_comments = $select_comments->rowCount();
					?>
					<i class='bx bxs-message-dots'></i> <!-- Icon for messages -->
					<h3><?= $numbers_of_comments; ?></h3>
					<p>New Messages</p>
					<a href="admin_message.php" class="btn">View Messages</a>
				</div>

				<!-- Canceled Orders Box -->
				<div class="box">
					<?php
					$select_canceled_order = $conn->prepare("SELECT * FROM `orders` WHERE status = ?");
					$select_canceled_order->execute(['canceled']);
					$total_canceled_order = $select_canceled_order->rowCount();
					?>
					<i class='bx bxs-error-alt'></i> <!-- Icon for canceled orders -->
					<h3><?= $total_canceled_order; ?></h3>
					<p>Canceled Orders</p>
					<a href="admin_order.php" class="btn">View Canceled Orders</a>
				</div>

				<!-- Orders In Progress Box -->
				<div class="box">
					<?php
					$select_confirm_order = $conn->prepare("SELECT * FROM `orders` WHERE status = ?");
					$select_confirm_order->execute(['in progress']);
					$total_confirm_order = $select_confirm_order->rowCount();
					?>
					<i class='bx bxs-truck'></i> <!-- Icon for in-progress orders -->
					<h3><?= $total_confirm_order; ?></h3>
					<p>Orders In Progress</p>
					<a href="admin_order.php" class="btn">View In Progress Orders</a>
				</div>

				<!-- Total Orders Placed Box -->
				<div class="box">
					<?php
					$select_total_order = $conn->prepare("SELECT * FROM `orders`");
					$select_total_order->execute();
					$total_total_order = $select_total_order->rowCount();
					?>
					<i class='bx bxs-cart'></i> <!-- Icon for total orders -->
					<h3><?= $total_total_order; ?></h3>
					<p>Total Orders Placed</p>
					<a href="admin_order.php" class="btn">View All Orders</a>
				</div>
			</div>
		</section>
	</div>
	<script src="script.js"></script>
</body>

</html>