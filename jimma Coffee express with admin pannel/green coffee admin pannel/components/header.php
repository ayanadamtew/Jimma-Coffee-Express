<header class="header">
	<div class="flex">
		<a href="home.php" class="logo"><img src="img/logo.jpg"></a>
		<nav class="navbar">
			<a href="home.php">home</a>
			<a href="view_products.php">products</a>
			<a href="order.php">orders</a>
			<a href="about.php">about us</a>
			<a href="contact.php">contact us</a>
		</nav>
		<div class="icons">
			<i class="bx bxs-user" id="user-btn"></i>
			<?php 
				$count_wishlist_items = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id = ?");
				$count_wishlist_items->execute([$user_id]);
				$total_wishlist_items = $count_wishlist_items->rowCount();
			?>
			<a href="wishlist.php" class="cart-btn"><i class="bx bx-heart"></i><sup><?=$total_wishlist_items ?></sup></a>
			<?php 
				$count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
				$count_cart_items->execute([$user_id]);
				$total_cart_items = $count_cart_items->rowCount();
			?>
			<a href="cart.php" class="cart-btn"><i class="bx bx-cart-download"></i><sup><?=$total_cart_items ?></sup></a>
			<i class='bx bx-list-plus' id="menu-btn" style="font-size: 2rem;"></i>
		</div>
		<?php 
				$select_profile = $conn->prepare("SELECT * FROM `users` WHERE id=?");
				$select_profile->execute([$user_id]);
				if ($select_profile->rowCount() > 0) {
					$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
				
		?>
		<div class="user-box">
			<p>username : <span><?php echo $_SESSION['user_name']; ?></span></p>
			<p>Email : <span><?php echo $_SESSION['user_email']; ?></span></p>
			
			<form method="post">
				<button type="submit" name="logout" class="logout-btn">log out</button>
			</form>
		</div>
		<?php }else{ ?>
			<div class="user-box">
				<p class="name">please login or register first!</p>
				<div class="flex-btn">
					<a href="login.php" class="btn" style="color: #000;">login</a>
					<a href="register.php" class="btn" style="color: #000;">register</a>
				</div>
			</div>	
			<?php } ?>
	</div>
</header>