<?php
require_once "includes/config.php";
session_start();

// Check if user is logged in and is a customer
if(!isset($_SESSION["user_id"]) || $_SESSION["user_type"] != "customer"){
    header("location: login.php");
    exit;
}

// Fetch user's orders
$sql = "SELECT o.*, 
        COUNT(oi.id) as total_items 
        FROM orders o 
        LEFT JOIN order_items oi ON o.id = oi.order_id 
        WHERE o.customer_id = ? 
        GROUP BY o.id 
        ORDER BY o.created_at DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION["user_id"]);
mysqli_stmt_execute($stmt);
$orders = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - AgroPlus</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .history-container {
            max-width: 1000px;
            margin: 80px auto;
            padding: 20px;
        }

        .history-header {
            text-align: center;
            margin-bottom: 40px;
            color: #2e7d32;
        }

        .order-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .order-header {
            background: #f5f5f5;
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .order-details {
            padding: 20px;
        }

        .order-items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .order-items th, .order-items td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9em;
        }

        .status-completed {
            background: #c8e6c9;
            color: #2e7d32;
        }

        .status-pending {
            background: #fff3e0;
            color: #ef6c00;
        }

        .status-processing {
            background: #e3f2fd;
            color: #1976d2;
        }

        .view-details {
            background: #4CAF50;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }

        .view-details:hover {
            background: #45a049;
        }

        .no-orders {
            text-align: center;
            padding: 40px;
            color: #666;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="history-container">
        <div class="history-header">
            <h1>Order History</h1>
            <p>View all your past orders and their details</p>
        </div>

        <?php if(mysqli_num_rows($orders) > 0): ?>
            <?php while($order = mysqli_fetch_assoc($orders)): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <strong>Order #<?php echo $order['id']; ?></strong>
                            <div style="color: #666; font-size: 0.9em;">
                                <?php echo date('d M Y, H:i', strtotime($order['created_at'])); ?>
                            </div>
                        </div>
                        <div>
                            <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="order-details">
                        <?php
                        // Fetch order items
                        $items_sql = "SELECT oi.*, p.name 
                                    FROM order_items oi 
                                    JOIN products p ON oi.product_id = p.id 
                                    WHERE oi.order_id = ?";
                        $stmt = mysqli_prepare($conn, $items_sql);
                        mysqli_stmt_bind_param($stmt, "i", $order['id']);
                        mysqli_stmt_execute($stmt);
                        $items = mysqli_stmt_get_result($stmt);
                        ?>
                        <table class="order-items">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($item = mysqli_fetch_assoc($items)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td>₹<?php echo number_format($item['price'], 2); ?></td>
                                        <td>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                                    <td><strong>₹<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                        <button onclick="window.print()" class="view-details">Print Invoice</button>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-orders">
                <h2>No orders found</h2>
                <p>You haven't placed any orders yet.</p>
                <a href="products.php" class="view-details">Start Shopping</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 