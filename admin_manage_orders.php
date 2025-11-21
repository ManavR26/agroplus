<?php
require_once "includes/config.php";
session_start();

// Check if user is logged in and is an admin
if(!isset($_SESSION["user_id"]) || $_SESSION["user_type"] != "admin"){
    header("location: admin_login.php");
    exit;
}

$success = $error = "";

// Handle order status updates
if(isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = strtolower($_POST['new_status']); // Convert to lowercase to match enum values
    
    $update_sql = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($stmt, "si", $new_status, $order_id);
    
    if(mysqli_stmt_execute($stmt)){
        $success = "Order status updated successfully!";
    } else {
        $error = "Error updating order status: " . mysqli_error($conn);
    }
}

// Fetch all orders with customer details
$sql = "SELECT o.*, u.username as customer_name 
        FROM orders o 
        LEFT JOIN users u ON o.customer_id = u.id 
        ORDER BY o.created_at DESC";
$orders = mysqli_query($conn, $sql);

// Check if the query was successful
if (!$orders) {
    error_log("Database query failed: " . mysqli_error($conn));
    die("Unable to load orders. Please try again later.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 80px auto 0;
            padding: 20px;
        }

        .orders-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border-radius: 5px;
            overflow: hidden;
        }

        .orders-table th,
        .orders-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .orders-table th {
            background: #4CAF50;
            color: white;
        }

        .orders-table tr:hover {
            background: #f5f5f5;
        }

        .status-select {
            padding: 6px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .update-btn {
            padding: 6px 12px;
            background: #2196F3;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .success { color: green; }
        .error { color: red; }
        
        .status-pending { color: #f39c12; }
        .status-processing { color: #3498db; }
        .status-completed { color: #2ecc71; }
        .status-cancelled { color: #e74c3c; }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="admin-container">
        <div class="header">
            <h1>Manage Orders</h1>
            <a href="admin_dashboard.php" class="back-btn">Back to Dashboard</a>
        </div>

        <?php if($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Order Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($order = mysqli_fetch_assoc($orders)): ?>
                    <tr>
                        <td>#<?php echo htmlspecialchars($order['id']); ?></td>
                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                        <td>₹<?php echo htmlspecialchars($order['total_amount']); ?></td>
                        <td class="status-<?php echo strtolower($order['status']); ?>">
                            <?php echo ucfirst(htmlspecialchars($order['status'])); ?>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="new_status" class="status-select">
                                    <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                <button type="submit" 
                                        name="update_status" 
                                        class="update-btn">
                                    Update
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html> 