<?php 
	 include '../components/connection.php';
	 session_start();

	 $admin_id = $_SESSION['admin_id'];

	 if (!isset($admin_id)) {
	 	header('location: admin_login.php');
	 }

	 if (isset($_POST['publish'])) {
	 	$id = unique_id();
	   	$title = $_POST['title'];
	   	$title = filter_var($title, FILTER_SANITIZE_STRING);

	   	$price = $_POST['price'];
	   	$price = filter_var($price, FILTER_SANITIZE_STRING);

	   	$content = $_POST['content'];
	   	$content = filter_var($content, FILTER_SANITIZE_STRING);

	   	$status = 'active';

	   	$image = $_FILES['image']['name'];
	   	$image = filter_var($image, FILTER_SANITIZE_STRING);
	   	$image_size = $_FILES['image']['size'];
	   	$image_tmp_name = $_FILES['image']['tmp_name'];
	   	$image_folder = '../image/'.$image;

	   	$select_image = $conn->prepare("SELECT * FROM `products` WHERE image = ?");
	   	$select_image->execute([$image]);

	   	if (isset($image)) {
	   		if ($select_image->rowCount() > 0) {
	   			$message[] = 'image name repeated!';
	   		}elseif($image_size > 2000000){
	   			$message[] = 'image size too large!';
	   		}else{
	   			move_uploaded_file($image_tmp_name, $image_folder);
	   		}
	   	}else{
	   		$image = '';
	   	}
	   	if ($select_image->rowCount() > 0 AND $image != '') {
	   		$message[] = 'please rename your image';
	   	}else{
	   		$insert_post = $conn->prepare("INSERT INTO `products`(id, name, price, image, product_detail, status) VALUES (?,?,?,?,?,?)");
	   		$insert_post->execute([$id, $title, $price, $image, $content, $status]);
	   		$message[] = 'post publish';
	   	}
	 }

	 //post adding in draft
	 if (isset($_POST['draft'])) {
	 	$id = unique_id();
	   	$title = $_POST['title'];
	   	$title = filter_var($title, FILTER_SANITIZE_STRING);

	   	$price = $_POST['price'];
	   	$price = filter_var($price, FILTER_SANITIZE_STRING);

	   	$content = $_POST['content'];
	   	$content = filter_var($content, FILTER_SANITIZE_STRING);

	   	$status = 'deactive';

	   	$image = $_FILES['image']['name'];
	   	$image = filter_var($image, FILTER_SANITIZE_STRING);
	   	$image_size = $_FILES['image']['size'];
	   	$image_tmp_name = $_FILES['image']['tmp_name'];
	   	$image_folder = '../image/'.$image;

	   	$select_image = $conn->prepare("SELECT * FROM `products` WHERE image = ? AND admin_id = ?");
	   	$select_image->execute([$image, $admin_id]);

	   	if (isset($image)) {
	   		if ($select_image->rowCount() > 0) {
	   			$message[] = 'image name repeated!';
	   		}elseif($image_size > 2000000){
	   			$message[] = 'image size too large!';
	   		}else{
	   			move_uploaded_file($image_tmp_name, $image_folder);
	   		}
	   	}else{
	   		$image = '';
	   	}
	   	if ($select_image->rowCount() > 0 AND $image != '') {
	   		$message[] = 'please rename your image';
	   	}else{
	   		$insert_post = $conn->prepare("INSERT INTO `products`(id, name, price, image, product_detail, status) VALUES (?,?,?,?,?,?)");
	   		$insert_post->execute([$id, $title, $price, $image, $content, $status]);
	   		$message[] = 'post publish';
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
			<h1>add products</h1>
		</div>
		<div class="title2">
			<a href="home.php">dashboard </a><span>/ add products </span>
		</div>
			
			<h1 class="heading">add product</h1>
			<div class="form-container">
				<form action="" method="post" enctype="multipart/form-data">
					<div class="input-field">
						<label>product name <sup>*</sup></label>
						<input type="text" name="title" maxlength="100" required placeholder="add post title">
					</div>
					<div class="input-field">
						<label>product price <sup>*</sup></label>
						<input type="number" name="price" maxlength="100" required placeholder="add post title">
					</div>
					<div class="input-field">
						<label>product detail<sup>*</sup></label>
						<textarea name="content" required maxlength="10000" placeholder="write your content.."></textarea>
					</div>
					
					<div class="input-field">
						<label>product image <sup>*</sup></label>
						<input type="file" name="image" accept="image/jpg, image/jpeg, image/png, image/webp" required>
					</div>
					<div class="flex-btn">
						<input type="submit" name="publish" value="publish post" class="btn">
						<input type="submit" name="draft" value="save draft" class="option-btn">
					</div>
				</form>
			</div>
		</section>
	</div>
	
	<script type="text/javascript" src="script.js"></script>
</body>
</html>