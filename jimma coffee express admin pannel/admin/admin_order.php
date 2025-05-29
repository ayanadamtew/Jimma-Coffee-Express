<?php
include '../components/connection.php';
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;

if (!isset($admin_id)) {
    header('location: admin_login.php');
    exit(); // Always exit after a header redirect
}

// Fetch admin profile for potential use (e.g., in header)
$fetch_profile = [];
try {
    $select_profile = $conn->prepare("SELECT * FROM `admin` WHERE id = ? LIMIT 1");
    $select_profile->execute([$admin_id]);
    if ($select_profile->rowCount() > 0) {
        $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    error_log("Error fetching admin profile in admin_order.php: " . $e->getMessage());
    header('location: admin_login.php');
    exit();
}


// --- Order Update Logic ---
if (isset($_POST['update_order'])) {
    $order_id = filter_var($_POST['order_id'], FILTER_SANITIZE_STRING);
    $update_payment_status = filter_var($_POST['update_payment_status'], FILTER_SANITIZE_STRING);

    try {
        $update_pay = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
        $update_pay->execute([$update_payment_status, $order_id]);
        $success_msg[] = 'Order payment status updated successfully!';
    } catch (PDOException $e) {
        error_log("Error updating order payment status: " . $e->getMessage());
        $warning_msg[] = 'Failed to update order payment status.';
    }
}

// --- Order Delete Logic ---
if (isset($_POST['delete_order'])) {
    $delete_id = filter_var($_POST['order_id'], FILTER_SANITIZE_STRING);

    try {
        $verify_delete = $conn->prepare("SELECT id FROM `orders` WHERE id = ?");
        $verify_delete->execute([$delete_id]);

        if ($verify_delete->rowCount() > 0) {
            $delete_order_query = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
            $delete_order_query->execute([$delete_id]);
            $success_msg[] = "Order deleted successfully!";
        } else {
            $warning_msg[] = 'Order not found or already deleted!';
        }
    } catch (PDOException $e) {
        error_log("Error deleting order: " . $e->getMessage());
        $warning_msg[] = 'Failed to delete order.';
    }
}

// --- Search and Sort Logic ---
$search_term = '';
if (isset($_GET['search_term']) && $_GET['search_term'] != '') {
    $search_term = filter_var($_GET['search_term'], FILTER_SANITIZE_STRING);
}

$sort_by = $_GET['sort_by'] ?? 'date'; // Default sort by date
$sort_order = $_GET['sort_order'] ?? 'desc'; // Default sort order descending

// Validate sort_by to prevent SQL injection for column names
$allowed_sort_columns = ['date', 'price', 'name', 'payment_status', 'status'];
if (!in_array($sort_by, $allowed_sort_columns)) {
    $sort_by = 'date'; // Fallback to default
}

// Validate sort_order
if (!in_array(strtolower($sort_order), ['asc', 'desc'])) {
    $sort_order = 'desc'; // Fallback to default
}

$sql_query = "SELECT * FROM `orders` WHERE 1=1"; // Base query

$params = [];

// Add search condition
if (!empty($search_term)) {
    $sql_query .= " AND (name LIKE ? OR email LIKE ? OR address LIKE ? OR id LIKE ?)";
    $search_param = '%' . $search_term . '%';
    $params = [$search_param, $search_param, $search_param, $search_param];
}

// Add sort condition
$sql_query .= " ORDER BY " . $sort_by . " " . $sort_order;

try {
    $select_orders = $conn->prepare($sql_query);
    $select_orders->execute($params);
    $orders_found = $select_orders->rowCount() > 0;
} catch (PDOException $e) {
    error_log("Error fetching orders: " . $e->getMessage());
    $error_msg[] = "An error occurred while fetching orders. Please try again later.";
    $orders_found = false;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Boxicons link -->
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <!-- Your dedicated admin_orders.css includes admin_style.css -->
    <link rel="stylesheet" href="../styles/admin_orders.css">
    <title>Admin Orders</title>
</head>

<body>
    <?php include '../components/admin_header.php'; ?>
    <div class="main">
        <div class="banner">
            <h1>Total Orders Placed</h1>
        </div>
        <div class="title2">
            <a href="dashboard.php">Home</a><span>/ Total Orders</span>
        </div>

        <section class="order-container">
            <h1 class="heading">Manage Orders</h1>

            <div class="order-controls">
                <!-- Search Form -->
                <div class="control-group">
                    <label for="search">Search Orders</label>
                    <form action="" method="GET" class="search-form" style="display: flex; gap: 1rem;">
                        <input type="search" id="search" name="search_term" placeholder="Search by name, email, ID..." value="<?= htmlspecialchars($search_term); ?>">
                        <button type="submit" class="btn"><i class='bx bx-search'></i> Search</button>
                    </form>
                </div>

                <!-- Sort Form -->
                <div class="control-group">
                    <label for="sort_by">Sort Orders By</label>
                    <form action="" method="GET" class="sort-form">
                        <input type="hidden" name="search_term" value="<?= htmlspecialchars($search_term); ?>">
                        <select name="sort_by" id="sort_by" onchange="this.form.submit()">
                            <option value="date" <?= ($sort_by == 'date') ? 'selected' : ''; ?>>Date</option>
                            <option value="price" <?= ($sort_by == 'price') ? 'selected' : ''; ?>>Price</option>
                            <option value="name" <?= ($sort_by == 'name') ? 'selected' : ''; ?>>Customer Name</option>
                            <option value="payment_status" <?= ($sort_by == 'payment_status') ? 'selected' : ''; ?>>Payment Status</option>
                            <option value="status" <?= ($sort_by == 'status') ? 'selected' : ''; ?>>Order Status</option>
                        </select>

                        <label for="sort_order" style="margin-top: 1.5rem;">Order</label>
                        <select name="sort_order" id="sort_order" onchange="this.form.submit()">
                            <option value="desc" <?= ($sort_order == 'desc') ? 'selected' : ''; ?>>Descending</option>
                            <option value="asc" <?= ($sort_order == 'asc') ? 'selected' : ''; ?>>Ascending</option>
                        </select>
                        <!-- No explicit submit button needed if using onchange -->
                        <div class="btn-group" style="margin-top: 1.5rem;">
                            <a href="admin_order.php" class="btn clear-btn"><i class='bx bx-reset'></i> Clear Filters</a>
                        </div>
                    </form>
                </div>
            </div>

            <?php
            if (!empty($search_term) || ($sort_by != 'date' || $sort_order != 'desc')) {
                echo '<p class="message info" style="margin-bottom: 2rem;">';
                if (!empty($search_term)) {
                    echo 'Showing results for: <strong>"' . htmlspecialchars($search_term) . '"</strong>. ';
                }
                echo 'Sorted by: <strong>' . htmlspecialchars(ucwords(str_replace('_', ' ', $sort_by))) . ' (' . htmlspecialchars(ucwords($sort_order)) . ')</strong>.</p>';
            }
            ?>

            <div class="box-container">
                <?php
                if ($orders_found) {
                    while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
                ?>
                        <div class="box">
                            <span class="status-badge <?= htmlspecialchars(str_replace(' ', '-', $fetch_orders['status'])); ?>">
                                <?= htmlspecialchars($fetch_orders['status']); ?>
                            </span>
                            <div class="detail">
                                <p><strong>Order ID:</strong> <span><?= htmlspecialchars($fetch_orders['id']); ?></span></p>
                                <p><strong>User Name:</strong> <span><?= htmlspecialchars($fetch_orders['name']); ?></span></p>
                                <p><strong>User ID:</strong> <span><?= htmlspecialchars($fetch_orders['user_id']); ?></span></p>
                                <p><strong>Placed On:</strong> <span><?= htmlspecialchars($fetch_orders['date']); ?></span></p>
                                <p><strong>Contact Number:</strong> <span><?= htmlspecialchars($fetch_orders['number']); ?></span></p>
                                <p><strong>Email:</strong> <span><?= htmlspecialchars($fetch_orders['email']); ?></span></p>
                                <p><strong>Total Price:</strong> <span>$<?= htmlspecialchars($fetch_orders['price']); ?></span></p>
                                <p><strong>Payment Method:</strong> <span><?= htmlspecialchars($fetch_orders['method']); ?></span></p>
                                <p><strong>Shipping Address:</strong> <span><?= htmlspecialchars($fetch_orders['address']); ?></span></p>
                            </div>
                            <form method="post">
                                <input type="hidden" name="order_id" value="<?= htmlspecialchars($fetch_orders['id']); ?>">
                                <label for="payment_status_<?= $fetch_orders['id']; ?>">Payment Status</label>
                                <select name="update_payment_status" id="payment_status_<?= $fetch_orders['id']; ?>">
                                    <option value="pending" <?= ($fetch_orders['payment_status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="complete" <?= ($fetch_orders['payment_status'] == 'complete') ? 'selected' : ''; ?>>Complete</option>
                                    <option value="canceled" <?= ($fetch_orders['payment_status'] == 'canceled') ? 'selected' : ''; ?>>Canceled</option>
                                </select>
                                <div class="flex-btn">
                                    <input type="submit" name="update_order" value="Update Status" class="btn">
                                    <input type="submit" name="delete_order" value="Delete Order" class="btn" onclick="confirmDelete(event, '<?= htmlspecialchars($fetch_orders['id']); ?>'); return false;">
                                </div>
                            </form>
                        </div>
                <?php
                    }
                } else {
                    echo '
                        <div class="empty">
                            <p>No orders found matching your criteria.</p>
                        </div>
                    ';
                }
                ?>
            </div>
        </section>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="script.js"></script>
    <?php include '../components/alert.php'; ?>

    <script>
        // SweetAlert for delete confirmation
        function confirmDelete(event, orderId) {
            event.preventDefault(); // Prevent the default form submission
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this order!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    // Create a temporary form to submit the delete request
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = ''; // Submit to the current page

                    const inputOrderId = document.createElement('input');
                    inputOrderId.type = 'hidden';
                    inputOrderId.name = 'order_id';
                    inputOrderId.value = orderId;
                    form.appendChild(inputOrderId);

                    const inputDeleteBtn = document.createElement('input');
                    inputDeleteBtn.type = 'hidden';
                    inputDeleteBtn.name = 'delete_order';
                    inputDeleteBtn.value = 'true'; // A value to trigger the delete logic
                    form.appendChild(inputDeleteBtn);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
</body>

</html>