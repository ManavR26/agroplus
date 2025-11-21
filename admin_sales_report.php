<?php
require_once "includes/config.php";
session_start();

// Check admin access
if(!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "admin"){
    header("location: admin_login.php");
    exit;
}

// Get date range from query parameters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Fetch sales statistics
$stats_query = "SELECT 
    COUNT(*) as total_orders,
    SUM(total_amount) as total_revenue,
    AVG(total_amount) as average_order,
    COUNT(DISTINCT customer_id) as unique_customers
FROM orders 
WHERE created_at BETWEEN ? AND ?";

$stmt = mysqli_prepare($conn, $stats_query);
mysqli_stmt_bind_param($stmt, "ss", $start_date, $end_date);
mysqli_stmt_execute($stmt);
$stats = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// Fetch top selling products
$products_query = "SELECT 
    p.name,
    p.price,
    SUM(oi.quantity) as total_sold,
    SUM(oi.quantity * oi.price) as revenue
FROM order_items oi
JOIN products p ON p.id = oi.product_id
JOIN orders o ON o.id = oi.order_id
WHERE o.created_at BETWEEN ? AND ?
GROUP BY p.id
ORDER BY total_sold DESC
LIMIT 10";

$stmt = mysqli_prepare($conn, $products_query);
mysqli_stmt_bind_param($stmt, "ss", $start_date, $end_date);
mysqli_stmt_execute($stmt);
$top_products = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report - Admin Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 80px auto 0;
            padding: 20px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-card h3 {
            color: #2e7d32;
            margin-bottom: 10px;
        }
        .stat-card .number {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .date-filter {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .products-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border-radius: 5px;
            overflow: hidden;
        }
        .products-table th,
        .products-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .products-table th {
            background: #4CAF50;
            color: white;
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
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="admin-container">
        <a href="admin_dashboard.php" class="back-btn">Back to Dashboard</a>
        
        <h1>Sales Report</h1>

        <div class="date-filter">
            <form method="GET">
                <label>Start Date:
                    <input type="date" name="start_date" value="<?php echo $start_date; ?>">
                </label>
                <label>End Date:
                    <input type="date" name="end_date" value="<?php echo $end_date; ?>">
                </label>
                <button type="submit">Filter</button>
            </form>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Orders</h3>
                <div class="number"><?php echo $stats['total_orders']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Revenue</h3>
                <div class="number">₹<?php echo number_format($stats['total_revenue'], 2); ?></div>
            </div>
            <div class="stat-card">
                <h3>Average Order</h3>
                <div class="number">₹<?php echo number_format($stats['average_order'], 2); ?></div>
            </div>
            <div class="stat-card">
                <h3>Unique Customers</h3>
                <div class="number"><?php echo $stats['unique_customers']; ?></div>
            </div>
        </div>

        <h2>Top Selling Products</h2>
        <table class="products-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Units Sold</th>
                    <th>Total Revenue</th>
                </tr>
            </thead>
            <tbody>
                <?php while($product = mysqli_fetch_assoc($top_products)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td>₹<?php echo number_format($product['price'], 2); ?></td>
                        <td><?php echo $product['total_sold']; ?></td>
                        <td>₹<?php echo number_format($product['revenue'], 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>