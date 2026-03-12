<header class="header">
    <div class="flex">
        <a href="home.php" class="logo"><img style="width: 320px;" src="image\Screenshot (30).png"></a>
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
            $count_wishlist_items = $conn->prepare("SELECT * FROM wishlist WHERE user_id = ?");
            $count_wishlist_items->execute([$user_id]);
            $total_wishlist_items = $count_wishlist_items->rowCount();
            ?>
            <a href="wishlist.php" class="cart-btn"><i class="bx bx-heart"></i><sup><?= $total_wishlist_items ?></sup></a>
            <?php
            $count_cart_items = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
            $count_cart_items->execute([$user_id]);
            $total_cart_items = $count_cart_items->rowCount();
            ?>
            <a href="cart.php" class="cart-btn"><i class="bx bx-cart-download"></i><sup><?= $total_cart_items ?></sup></a>
            <i class='bx bx-list-plus' id="menu-btn" style="font-size: 2rem;"></i>
        </div>
        <div class="user-box">
            <p><span><?php echo isset($_SESSION['name']) ? $_SESSION['name'] : ''; ?></span></p>
            <p><span><?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?></span></p>
            <a href="register.php" class="btn" style="color: #000;">register</a>
            <form method="post">
                <button type="submit" name="logout" class="logout-btn">log out</button>
            </form>
        </div>
    </div>
</header>