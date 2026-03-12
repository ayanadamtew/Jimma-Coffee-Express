<?php
include '../components/connection.php';
session_start();

// --- Session Guard ---
if (!isset($_SESSION['admin_id'])) {
	header('location: admin_login.php');
	exit;
}

$admin_id = $_SESSION['admin_id'];

// --- Add Product ---
if (isset($_POST['add_product'])) {
	$product_id = unique_id();
	$product_name = trim(filter_var($_POST['name'], FILTER_SANITIZE_STRING));
	$product_price = trim(filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
	$product_detail = trim(filter_var($_POST['detail'], FILTER_SANITIZE_STRING));

	// Validate image upload
	$allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
	$image_tmp = $_FILES['image']['tmp_name'];
	$image_size = $_FILES['image']['size'];
	$image_type = mime_content_type($image_tmp);
	$image_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

	if (!in_array($image_type, $allowed_types)) {
		$message[] = 'Invalid image type. Only JPG, PNG, WEBP, GIF allowed.';
	}
	elseif ($image_size > 2 * 1024 * 1024) {
		$message[] = 'Image size must be under 2MB.';
	}
	else {
		// Check duplicate product name
		$check = $conn->prepare("SELECT id FROM `products` WHERE name = ?");
		$check->execute([$product_name]);

		if ($check->rowCount() > 0) {
			$message[] = 'Product name already exists.';
		}
		else {
			// Use unique ID as filename to prevent path traversal
			$image_filename = unique_id() . '.' . $image_ext;
			$image_folder = '../image/' . $image_filename;

			move_uploaded_file($image_tmp, $image_folder);

			$insert = $conn->prepare("INSERT INTO `products`(id, name, price, product_detail, image, status) VALUES(?, ?, ?, ?, ?, 'active')");
			$insert->execute([$product_id, $product_name, $product_price, $product_detail, $image_filename]);
			$message[] = 'Product added successfully!';
		}
	}
}

// --- Delete Product ---
if (isset($_GET['delete'])) {
	$delete_id = trim($_GET['delete']);

	$img_stmt = $conn->prepare("SELECT image FROM `products` WHERE id = ?");
	$img_stmt->execute([$delete_id]);
	$img_row = $img_stmt->fetch();

	if ($img_row) {
		$img_path = '../image/' . $img_row['image'];
		if (file_exists($img_path)) {
			unlink($img_path);
		}
	}

	$conn->prepare("DELETE FROM `products` WHERE id = ?")->execute([$delete_id]);
	$conn->prepare("DELETE FROM `cart` WHERE product_id = ?")->execute([$delete_id]);
	$conn->prepare("DELETE FROM `wishlist` WHERE product_id = ?")->execute([$delete_id]);

	header('location: admin_product.php');
	exit;
}

// --- Update Product ---
if (isset($_POST['update_product'])) {
	$update_id = trim($_POST['update_id']);
	$update_name = trim(filter_var($_POST['update_name'], FILTER_SANITIZE_STRING));
	$update_price = trim(filter_var($_POST['update_price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
	$update_detail = trim(filter_var($_POST['update_detail'], FILTER_SANITIZE_STRING));

	// Fetch existing image
	$existing = $conn->prepare("SELECT image FROM `products` WHERE id = ?");
	$existing->execute([$update_id]);
	$existing_row = $existing->fetch();
	$image_filename = $existing_row['image'];

	// If a new image was uploaded, replace it
	if (!empty($_FILES['update_image']['name'])) {
		$allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
		$image_tmp = $_FILES['update_image']['tmp_name'];
		$image_size = $_FILES['update_image']['size'];
		$image_type = mime_content_type($image_tmp);
		$image_ext = strtolower(pathinfo($_FILES['update_image']['name'], PATHINFO_EXTENSION));

		if (!in_array($image_type, $allowed_types)) {
			$message[] = 'Invalid image type.';
		}
		elseif ($image_size > 2 * 1024 * 1024) {
			$message[] = 'Image too large (max 2MB).';
		}
		else {
			// Delete old image
			$old_path = '../image/' . $image_filename;
			if (file_exists($old_path)) {
				unlink($old_path);
			}
			$image_filename = unique_id() . '.' . $image_ext;
			move_uploaded_file($image_tmp, '../image/' . $image_filename);
		}
	}

	if (!isset($message)) {
		$update_stmt = $conn->prepare("UPDATE `products` SET name=?, price=?, product_detail=?, image=? WHERE id=?");
		$update_stmt->execute([$update_name, $update_price, $update_detail, $image_filename, $update_id]);
		header('location: admin_product.php');
		exit;
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
	<link rel="stylesheet" href="admin_style.css">
	<title>Admin — Products | Jimma Coffee Express</title>
</head>

<body>
	<?php include '../components/admin_header.php'; ?>

	<?php if (isset($message)): ?>
	<?php foreach ($message as $msg): ?>
	<div class="message">
		<span>
			<?= htmlspecialchars($msg)?>
		</span>
		<i class="bi bi-x-circle" onclick="this.parentElement.remove()"></i>
	</div>
	<?php
	endforeach; ?>
	<?php
endif; ?>

	<div class="line2"></div>

	<section class="add-products form-container">
		<form method="POST" action="" enctype="multipart/form-data">
			<div class="input-field">
				<label>Product Name</label>
				<input type="text" name="name" required maxlength="100">
			</div>
			<div class="input-field">
				<label>Product Price (ETB)</label>
				<input type="number" name="price" required min="0" step="0.01">
			</div>
			<div class="input-field">
				<label>Product Description</label>
				<textarea name="detail" required maxlength="500"></textarea>
			</div>
			<div class="input-field">
				<label>Product Image (JPG/PNG/WEBP, max 2MB)</label>
				<input type="file" name="image" accept="image/jpg, image/jpeg, image/png, image/webp" required>
			</div>
			<input type="submit" name="add_product" value="add product" class="btn">
		</form>
	</section>

	<div class="line3"></div>

	<section class="show-products">
		<div class="box-container">
			<?php
$select_products = $conn->prepare("SELECT * FROM `products`");
$select_products->execute();
if ($select_products->rowCount() > 0):
	while ($p = $select_products->fetch()):
?>
			<div class="box">
				<img src="../image/<?= htmlspecialchars($p['image'])?>" alt="product">
				<p>price:
					<?= htmlspecialchars($p['price'])?> ETB
				</p>
				<h4>
					<?= htmlspecialchars($p['name'])?>
				</h4>
				<p class="status">
					<?= htmlspecialchars($p['status'])?>
				</p>
				<a href="admin_product.php?edit=<?= urlencode($p['id'])?>" class="edit">edit</a>
				<a href="admin_product.php?delete=<?= urlencode($p['id'])?>" class="delete"
					onclick="return confirm('Delete this product?');">delete</a>
			</div>
			<?php
	endwhile;
else:
	echo '<div class="empty"><p>No products added yet.</p></div>';
endif;
?>
		</div>
	</section>

	<div class="line"></div>

	<section class="update-container">
		<?php if (isset($_GET['edit'])): ?>
		<?php
	$edit_id = trim($_GET['edit']);
	$edit_q = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
	$edit_q->execute([$edit_id]);
	$fe = $edit_q->fetch();
?>
		<?php if ($fe): ?>
		<form method="POST" enctype="multipart/form-data">
			<img src="../image/<?= htmlspecialchars($fe['image'])?>" alt="current image">
			<input type="hidden" name="update_id" value="<?= htmlspecialchars($fe['id'])?>">
			<input type="text" name="update_name" value="<?= htmlspecialchars($fe['name'])?>" required>
			<input type="number" name="update_price" min="0" step="0.01" value="<?= htmlspecialchars($fe['price'])?>"
				required>
			<textarea name="update_detail" required><?= htmlspecialchars($fe['product_detail'])?></textarea>
			<input type="file" name="update_image" accept="image/jpg, image/jpeg, image/png, image/webp">
			<small>Leave image blank to keep existing image.</small>
			<input type="submit" name="update_product" value="update" class="edit">
			<input type="reset" value="cancel" class="option-btn btn" id="close-form">
		</form>
		<script>document.querySelector('.update-container').style.display = 'block'</script>
		<?php
	endif; ?>
		<?php
endif; ?>
	</section>

	<script src="script.js"></script>
	<script>
		const closeBtn = document.querySelector('#close-form');
		if (closeBtn) {
			closeBtn.addEventListener('click', () => {
				document.querySelector('.update-container').style.display = 'none';
			});
		}
	</script>
</body>

</html>