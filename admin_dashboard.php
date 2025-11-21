
<?php
require_once "includes/config.php";
session_start();

// Strict admin check
if(!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "admin"){
    header("location: admin_login.php");
    exit;
}

// Prevent caching to avoid viewing after logout via back button
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Fetch statistics for the dashboard
$stats = [
    'farmers' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE user_type='farmer'"))['count'],
    'customers' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE user_type='customer'"))['count'],
    'products' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM products"))['count'],
    'orders' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM orders"))['count']
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AgroPlus</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f5f5f5;
        }
        
        .admin-container {
            max-width: 1200px;
            margin: 80px auto 20px; /* Added top margin for navbar */
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .logout-btn {
            padding: 8px 16px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .logout-btn:hover {
            background: #c82333;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .logout-btn {
            padding: 8px 16px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .logout-btn:hover {
            background: #c82333;
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

        .admin-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
        }

        .admin-card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .admin-card h2 {
            color: #2e7d32;
            margin-bottom: 20px;
        }

        .admin-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
            margin-right: 10px;
        }

        .admin-btn:hover {
            background: #2e7d32;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <script>
        // Force logout when this page is reached via back/forward navigation
        (function() {
            // Handle bfcache restores
            window.onpageshow = function(event) {
                if (event.persisted) {
                    window.location.replace('logout.php');
                }
            };
            // Handle typical back/forward navigation
            if (window.performance && window.performance.navigation && window.performance.navigation.type === 2) {
                window.location.replace('logout.php');
            }
        })();
    </script>

    <div class="admin-container">
        <div class="header">
            <h1>Admin Dashboard</h1>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Farmers</h3>
                <div class="number"><?php echo $stats['farmers']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Customers</h3>
                <div class="number"><?php echo $stats['customers']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Products</h3>
                <div class="number"><?php echo $stats['products']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Orders</h3>
                <div class="number"><?php echo $stats['orders']; ?></div>
            </div>
        </div>

        <div class="admin-grid">
            <div class="admin-card">
                <h2>User Management</h2>
                <a href="admin_manage_farmers.php" class="admin-btn">Manage Farmers</a>
                <a href="admin_manage_customers.php" class="admin-btn">Manage Customers</a>
            </div>

            <div class="admin-card">
                <h2>Product Management</h2>
                <a href="admin_manage_products.php" class="admin-btn">Manage Products</a>
                <a href="admin_manage_orders.php" class="admin-btn">Manage Orders</a>
            </div>

            <div class="admin-card">
                <h2>Content Management</h2>
                <a href="admin_waste_management.php" class="admin-btn">Manage Waste Management</a>
                <a href="admin_organic_methods.php" class="admin-btn">Manage Organic Methods</a>
                <a href="admin_government_schemes.php" class="admin-btn">Manage Government Schemes</a>
            </div>
        </div>
    </div>
</body>
</html> 