<?php 
	 include '../components/connection.php';
	 session_start();

	 $admin_id = $_SESSION['admin_id'];

	 if (!isset($admin_id)) {
	 	header('location: admin_login.php');
	 }
	 if (isset($_POST['delete'])) {
	 	$delete_image = $conn->prepare("SELECT * FROM `posts` WHERE admin_id = ?");
	 	$delete_image->execute([$admin_id]);
	 	while($fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC)){
	 		unlink('../update_image/'.$fetch_delete_image);
	 	}

	 	$delete_posts = $conn->prepare("DELETE FROM `posts` WHERE admin_id = ?");
	 	$delete_posts->execute([$admin_id]);
	 	$delete_likes = $conn->prepare("DELETE FROM `likes` WHERE admin_id = ?");
	 	$delete_likes->execute([$admin_id]);
	 	$delete_comment = $conn->prepare("DELETE FROM `comments` WHERE admin_id = ?");
	 	$delete_comment->execute([$admin_id]);

	 	$delete_admin = $conn->prepare("DELETE FROM `admin` WHERE id = ?");
	 	$delete_admin->execute([$admin_id]);

	 	header('location:../components/admin_logout.php');

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
				<h1>admin account</h1>
			</div>
			<div class="title2">
				<a href="home.php">home </a><span>/ admin account</span>
			</div>
			<section class="accounts">
				<h1 class="heading">admin account</h1>
				<div class="box-container">
					<?php 
	 					$select_admin = $conn->prepare("SELECT * FROM `admin`");
	 					$select_admin->execute();
	 					if ($select_admin->rowCount() > 0) {
	 						while($fetch_accounts = $select_admin->fetch(PDO::FETCH_ASSOC)){
	 							$user_id = $fetch_accounts['id'];
	 							
	 						
					?>
					<div class="box">
						<div class="profile">
							<img src="../image/<?= $fetch_profile['profile']; ?>" class="logo-image" width="100">
						</div>
						<p>admin id : <span><?= $user_id; ?></span></p>
						<p>admin name : <span><?= $fetch_accounts['name']; ?></span></p>
						<p>admin email : <span><?= $fetch_accounts['email']; ?></span></p>
						<div class="flex-btn">
							<a href="update_profile.php" class="btn">update profile</a>
							<a href="components/admin_logout.php" onclick="return confirm('logout from this website')" class="btn">logout</a>
						</div>
					</div>
					<?php 
							}
	 					}else{
	 						echo '
								<div class="empty">
									<p>no post found!</p>
								</div>
							';
	 					}
					?>
				</div>
			</section>
		</div>
	
	<script type="text/javascript" src="script.js"></script>
</body>
</html>