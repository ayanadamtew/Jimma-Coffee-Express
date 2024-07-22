<?php
include '../components/connection.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
	header('location: admin_login.php');
}

//delete post from database

if (isset($_POST['delete'])) {
	$p_id = $_POST['product_id'];
	$p_id = filter_var($p_id, FILTER_SANITIZE_STRING);


	$delete_post = $conn->prepare("DELETE FROM `product` WHERE id = ?");
	$delete_post->execute([$p_id]);

	$message[] = 'post deleted successfully';
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
			<h1>all products</h1>
		</div>
		<div class="title2">
			<a href="home.php">home </a><span>/ all products</span>
		</div>
		<section class="post-editor">
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

			<h1 class="heading">your post</h1>
			<div class="show-post">
				<div class="box-container">
					<?php
					$select_posts = $conn->prepare("SELECT * FROM `product`");
					$select_posts->execute();
					if ($select_posts->rowCount() > 0) {
						while ($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)) {

					?>
							<form method="post" class="box <?php echo $fetch_posts['status']; ?>">
								<input type="hidden" name="product_id" value="<?= $fetch_posts['id']; ?>">
								<?php if ($fetch_posts['image'] != '') { ?>
									<img src="../image/<?= $fetch_posts['image'] ?>" class="image">
								<?php } ?>
								<div class="status"><?= $fetch_posts['status'] ?></div>

								<div class="price">$<?= $fetch_posts['price'] ?>/-</div>
								<div class="title"><?= $fetch_posts['name'] ?></div>
								<div class="flex-btn">
									<a href="edit_post.php?id=<?= $fetch_posts['id']; ?>" class="btn">edit</a>
									<button type="submit" name="delete" class="btn" onclick="return confirm('delete this post?')">delete</button>
									<a href="read_posts.php?post_id=<?= $fetch_posts['id']; ?>" class="btn">view post</a>
								</div>
							</form>
					<?php
						}
					} else {

						echo '
								<div class="empty">
									<p>no post added yet! <br><a href="add_posts.php" class="btn" style="margin-top: 1.5rem;">add post</a></p>
								</div>
							';
					}
					?>
				</div>

			</div>
		</section>
	</div>

	<script>
		function showActivePosts() {
			const posts = document.querySelectorAll('.box');
			posts.forEach((post) => {
				const status = post.querySelector('.status');
				if (status.textContent === 'active') {
					post.style.display = 'block';
				} else {
					post.style.display = 'none';
				}
			});
		}

		function showInactivePosts() {
			const posts = document.querySelectorAll('.box');
			posts.forEach((post) => {
				const status = post.querySelector('.status');
				if (status.textContent === 'deactive') {
					post.style.display = 'block';
				} else {
					post.style.display = 'none';
				}
			});
		}
	</script>
</body>

</html>