<?php
include '../components/connection.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
	header('location: admin_login.php');
}

// Function to delete a user
function deleteUser($conn, $userId)
{

	// Delete user from the 'users' table
	$deleteUser = $conn->prepare("DELETE FROM `users` WHERE id = ?");
	$deleteUser->execute([$userId]);
	return true;
}

if (isset($_POST['delete_user'])) {
	$userIdToDelete = $_POST['user_id_to_delete'];

	if (deleteUser($conn, $userIdToDelete)) {
		$success_msg[] = 'User deleted successfully';
	} else {
		$error_msg[] = 'Failed to delete user';
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
	<title>admin dashboard</title>
</head>

<body>
	<?php include '../components/admin_header.php'; ?>
	<div class="main">
		<div class="banner">
			<h1>Registered Users</h1>
		</div>
		<div class="title2">
			<a href="home.php">Home</a><span>/ Registered Users</span>
		</div>
		<section class="accounts">
			<h1 class="heading">Users Account</h1>
			<div class="box-container">
				<!-- Search Form -->
				<form method="get" action="">
					<input type="text" name="search" placeholder="Search by name">
					<button type="submit">Search</button>
				</form>
				<?php
				// Check if a search query is present
				$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

				// SQL query with search condition
				$select_users = $conn->prepare("SELECT * FROM `users` WHERE `name` LIKE ? ESCAPE '!'");
				$select_users->execute(["%" . $searchQuery . "%"]);

				if ($select_users->rowCount() > 0) {
					while ($fetch_accounts = $select_users->fetch(PDO::FETCH_ASSOC)) {
						$user_id = $fetch_accounts['id'];
				?>
						<div class="box">
							<form method="post" onsubmit="return confirm('Are you sure you want to delete this user?');">
								<input type="hidden" name="user_id_to_delete" value="<?= $user_id; ?>">
								<button type="submit" name="delete_user" class="delete-btn">Delete</button>
							</form>
							<p>User ID: <span><?= $user_id; ?></span></p>
							<p>User Name: <span><?= $fetch_accounts['name']; ?></span></p>
							<p>User Email: <span><?= $fetch_accounts['email']; ?></span></p>
							<p>User Type: <span><?= $fetch_accounts['user_type']; ?></span></p>
						</div>
				<?php
					}
				} else {
					echo '<div class="empty"><p>No users found!</p></div>';
				}
				?>
			</div>
		</section>
	</div>
	<script type="text/javascript" src="script.js"></script>
</body>

</html>