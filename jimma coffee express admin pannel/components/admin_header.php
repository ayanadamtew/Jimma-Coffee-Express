<?php
// This header component needs access to the admin_id and $conn object.
// It also needs session_start() to be called *before* any output.
// Assuming connection.php and session_start() are already handled in the main page (e.g., dashboard.php)
// If not, you might need to uncomment them here.

// include '../components/connection.php'; // Uncomment if connection is not handled by parent script
// session_start(); // Uncomment if session is not started by parent script

$admin_id = $_SESSION['admin_id'] ?? null; // Use null coalescing to prevent undefined index if not set

$fetch_profile = []; // Initialize to prevent errors if profile isn't found
if (isset($admin_id)) {
    try {
        $select_profile = $conn->prepare("SELECT * FROM `admin` WHERE id = ? LIMIT 1");
        $select_profile->execute([$admin_id]);
        if ($select_profile->rowCount() > 0) {
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        // Log the error for debugging, do not display sensitive info to user
        // error_log("Error fetching admin profile: " . $e->getMessage());
        // You might want to redirect or show a generic error message
        header('location: admin_login.php'); // Redirect if profile fetch fails or admin_id is invalid
        exit();
    }
} else {
    // If admin_id is not set in session, redirect to login
    header('location: admin_login.php');
    exit();
}
?>

<header class="header">
    <div class="flex">
        <a href="dashboard.php" class="logo">Admin<span>Panel</span></a>

        <nav class="navbar">
            <a href="dashboard.php">Dashboard</a>
            <a href="add_posts.php">Products</a>
            <a href="admin_accounts.php">Admins</a>
            <a href="user_accounts.php">Users</a>
            <a href="admin_message.php">Messages</a>
            <a href="admin_order.php">Orders</a>
        </nav>

        <div class="icons">
            <i class="bx bxs-user" id="user-btn"></i> <!-- Icon for user profile -->
            <i class="bx bx-menu" id="menu-btn"></i> <!-- Icon for mobile menu -->
        </div>

        <div class="user-box">
            <p>username : <span><?= htmlspecialchars($fetch_profile['name'] ?? 'Guest'); ?></span></p>
            <p>email : <span><?= htmlspecialchars($fetch_profile['email'] ?? 'N/A'); ?></span></p>
            <a href="update_profile.php" class="btn">Update Profile</a>
            <a href="../components/admin_logout.php" class="delete-btn">Logout</a>
        </div>
    </div>
</header>