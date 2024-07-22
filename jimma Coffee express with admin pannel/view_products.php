<?php
include 'components/connection.php';
session_start();

function fetchProductDetails($conn, $product_id)
{
	$select_price = $conn->prepare("SELECT * FROM `product` WHERE id = ? LIMIT 1");
	$select_price->execute([$product_id]);
	return $select_price->fetch(PDO::FETCH_ASSOC);
}

if (isset($_SESSION['user_id'])) {
	$user_id = $_SESSION['user_id'];
} else {
	$user_id = '';
}

if (isset($_POST['logout'])) {
	session_destroy();
	header("location: login.php");
	exit;
}

// adding products in wishlist
if (isset($_POST['add_to_wishlist'])) {
	$id = uniqid();
	$product_id = $_POST['product_id'];

	$verify_wishlist = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id = ? AND product_id = ?");
	$verify_wishlist->execute([$user_id, $product_id]);

	$cart_num = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ? AND product_id = ?");
	$cart_num->execute([$user_id, $product_id]);

	if ($verify_wishlist->rowCount() > 0) {
		$warning_msg[] = 'Product already exists in your wishlist';
	} elseif ($cart_num->rowCount() > 0) {
		$warning_msg[] = 'Product already exists in your cart';
	} else {
		$fetch_price = fetchProductDetails($conn, $product_id);

		$insert_wishlist = $conn->prepare("INSERT INTO `wishlist` (id, user_id, product_id, price) VALUES (?, ?, ?, ?)");
		$insert_wishlist->execute([$id, $user_id, $product_id, $fetch_price['price']]);
		$success_msg[] = 'Product added to wishlist successfully';
	}
}

// adding products in cart
if (isset($_POST['add_to_cart'])) {
	$id = uniqid();
	$product_id = $_POST['product_id'];

	$qty = filter_var($_POST['qty'], FILTER_SANITIZE_STRING);

	$verify_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ? AND product_id = ?");
	$verify_cart->execute([$user_id, $product_id]);

	$max_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
	$max_cart_items->execute([$user_id]);

	if ($verify_cart->rowCount() > 0) {
		$warning_msg[] = 'Product already exists in your cart';
	} elseif ($max_cart_items->rowCount() > 20) {
		$warning_msg[] = 'Cart is full';
	} else {
		$fetch_price = fetchProductDetails($conn, $product_id);

		$insert_cart = $conn->prepare("INSERT INTO `cart` (id, user_id, product_id, price, qty) VALUES (?, ?, ?, ?, ?)");
		$insert_cart->execute([$id, $user_id, $product_id, $fetch_price['price'], $qty]);
		$success_msg[] = 'Product added to cart successfully';
	}
}

// Pagination
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$records_per_page = 5;
$offset = ($page - 1) * $records_per_page;

$select_products = $conn->prepare("SELECT * FROM `product` ORDER BY price DESC LIMIT $offset, $records_per_page");
$select_products->execute();

$total_products = $conn->query("SELECT count(*) as total_records FROM `product`")->fetch()['total_records'];
$total_pages = ceil($total_products / $records_per_page);

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
	<title>jimma Coffee express - shop page</title>
</head>

<body>
	<?php include 'components/header.php'; ?>
	<div class="main">
		<div class="banner">
			<h1>shop</h1>
		</div>
		<div class="title2">
			<a href="home.php">home </a><span>/ our shop</span>
		</div>
		<section class="products">
			<div class="box-container">
				<?php
				if ($select_products->rowCount() > 0) {
					while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
				?>
						<form action="" method="post" class="box">
							<img src="image/<?= $fetch_products['image']; ?>" class="img">
							<div class="button">
								<button type="submit" name="add_to_cart"><i class="bx bx-cart"></i></button>
								<button type="submit" name="add_to_wishlist"><i class="bx bx-heart"></i></button>
								<a href="view_page.php?pid=<?php echo $fetch_products['id']; ?>" class="bx bxs-show"></a>
							</div>
							<h3 class="name"><?= $fetch_products['name']; ?></h3>
							<input type="hidden" name="product_id" value="<?= $fetch_products['id']; ?>">
							<div class="flex">
								<p class="price">price $<?= $fetch_products['price']; ?>/-</p>
								<input type="number" name="qty" required min="1" value="1" max="99" maxlength="2" class="qty">
							</div>
							<a href="checkout.php?get_id=<?= $fetch_products['id']; ?>" class="btn">buy now</a>

						</form>
				<?php
					}
				} else {
					echo '<p class="empty">no products added yet!</p>';
				}
				?>
			</div>
			<!-- Pagination -->
			<div class="pagination">
				<?php if ($total_pages > 1) : ?>
					<?php if ($page > 1) : ?>
						<a href="?page=<?= $page - 1 ?>" class="prev">Previous</a>
					<?php endif; ?>
					<?php for ($i = 1; $i <= $total_pages; $i++) : ?>
						<a href="?page=<?= $i ?>" class="<?= ($page == $i) ? 'active' : '' ?>"><?= $i ?></a>
					<?php endfor; ?>
					<?php if ($page < $total_pages) : ?>
						<a href="?page=<?= $page + 1 ?>" class="next">Next</a>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</section>
		<?php include 'components/footer.php'; ?>
	</div>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert