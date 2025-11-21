<?php
require_once "includes/config.php";
session_start();

// Require admin session
if(!isset($_SESSION["user_id"]) || $_SESSION["user_type"] != "admin"){
    header("location: admin_login.php");
    exit;
}

// Validate id
$customer_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if($customer_id <= 0){
    http_response_code(404);
    echo "Invalid customer id.";
    exit;
}

// Fetch customer details
$customer = null;
if($stmt = mysqli_prepare($conn, "SELECT id, username, email, created_at, mobile, address FROM users WHERE id = ? AND user_type = 'customer'")){
    mysqli_stmt_bind_param($stmt, "i", $customer_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $customer = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

if(!$customer){
    http_response_code(404);
    echo "Customer not found.";
    exit;
}

// Counts and aggregates
$order_count = 0;
$total_spent = 0.0;
if($stmt = mysqli_prepare($conn, "SELECT COUNT(*) as c, COALESCE(SUM(total_amount),0) as s FROM orders WHERE customer_id = ?")){
    mysqli_stmt_bind_param($stmt, "i", $customer_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    if($row){
        $order_count = intval($row['c']);
        $total_spent = (float)$row['s'];
    }
    mysqli_stmt_close($stmt);
}

// Fetch orders and item counts
$orders = [];
$sql = "SELECT o.id, o.total_amount, o.status, o.created_at, 
               COALESCE(items.total_items, 0) as total_items
        FROM orders o
        LEFT JOIN (
            SELECT order_id, COUNT(*) as total_items
            FROM order_items
            GROUP BY order_id
        ) items ON items.order_id = o.id
        WHERE o.customer_id = ?
        ORDER BY o.created_at DESC";
if($stmt = mysqli_prepare($conn, $sql)){
    mysqli_stmt_bind_param($stmt, "i", $customer_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while($row = mysqli_fetch_assoc($res)){
        $orders[] = $row;
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Details - Admin</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .admin-container { max-width: 1100px; margin: 80px auto 0; padding: 20px; }
        .back-btn { display: inline-block; padding: 10px 20px; background: #4CAF50; color: #fff; text-decoration: none; border-radius: 5px; margin-bottom: 20px; }
        .card { background: #fff; border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); padding: 20px; margin-bottom: 20px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; }
        .stat { background: #f6faf7; border: 1px solid #e1efe5; border-radius: 6px; padding: 16px; }
        .table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); border-radius: 5px; overflow: hidden; }
        .table th, .table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        .table th { background: #673AB7; color: #fff; }
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', Arial, sans-serif; }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="admin-container">
        <a href="admin_manage_customers.php" class="back-btn">Back to Customers</a>

        <div class="card">
            <h1 style="margin: 0 0 10px;">Customer #<?php echo $customer['id']; ?> - <?php echo htmlspecialchars($customer['username']); ?></h1>
            <div class="grid">
                <div class="stat">
                    <strong>Email</strong><br>
                    <?php echo htmlspecialchars($customer['email']); ?>
                </div>
                <div class="stat">
                    <strong>Joined</strong><br>
                    <?php echo date('M d, Y', strtotime($customer['created_at'])); ?>
                </div>
                <div class="stat">
                    <strong>Mobile</strong><br>
                    <?php echo htmlspecialchars((string)($customer['mobile'] ?? '—')); ?>
                </div>
                <div class="stat">
                    <strong>Address</strong><br>
                    <?php echo htmlspecialchars((string)($customer['address'] ?? '—')); ?>
                </div>
                <div class="stat">
                    <strong>Total Orders</strong><br>
                    <?php echo $order_count; ?>
                </div>
                <div class="stat">
                    <strong>Total Spent</strong><br>
                    ₹ <?php echo number_format($total_spent, 2); ?>
                </div>
            </div>
        </div>

        <div class="card">
            <h2 style="margin-top: 0;">Orders</h2>
            <?php if(empty($orders)): ?>
                <p>No orders found for this customer.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Status</th>
                            <th>Total Items</th>
                            <th>Total Amount</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($orders as $o): ?>
                            <tr>
                                <td><?php echo $o['id']; ?></td>
                                <td><?php echo htmlspecialchars($o['status']); ?></td>
                                <td><?php echo intval($o['total_items']); ?></td>
                                <td><?php echo number_format((float)$o['total_amount'], 2); ?></td>
                                <td><?php echo date('M d, Y', strtotime($o['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>


