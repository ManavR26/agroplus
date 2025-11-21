<?php
require_once "includes/config.php";
session_start();

// Check if user is logged in and is an admin
if(!isset($_SESSION["user_id"]) || $_SESSION["user_type"] != "admin"){
    header("location: admin_login.php");
    exit;
}

// Handle customer deletion
if(isset($_POST['delete_customer'])) {
    $customer_id = intval($_POST['customer_id']); // Sanitize input
    $delete_sql = "DELETE FROM users WHERE id = ? AND user_type = 'customer'";
    $stmt = mysqli_prepare($conn, $delete_sql);
    if($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $customer_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

// Fetch all customers
$sql = "SELECT * FROM users WHERE user_type = 'customer' ORDER BY created_at DESC";
$customers = mysqli_query($conn, $sql);

// Removed unrelated government_schemes query that caused fatal error when table absent
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Customers - Admin Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 80px auto 0;
            padding: 20px;
        }

        .customers-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border-radius: 5px;
            overflow: hidden;
        }

        .customers-table th,
        .customers-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .customers-table th {
            background: #4CAF50;
            color: white;
        }

        .customers-table tr:hover {
            background: #f5f5f5;
        }

        .action-btn {
            padding: 6px 12px;
            border-radius: 3px;
            text-decoration: none;
            color: white;
            font-size: 14px;
        }

        .view-btn {
            background: #2196F3;
        }

        .delete-btn {
            background: #f44336;
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

        .government-schemes {
            background-image: url('assets/images/government-schemes.jpg');
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="admin-container">
        <a href="admin_dashboard.php" class="back-btn">Back to Dashboard</a>
        
        <h1>Manage Customers</h1>

        <table class="customers-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Join Date</th>
                    <th>Orders</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($customer = mysqli_fetch_assoc($customers)): ?>
                    <tr>
                        <td><?php echo $customer['id']; ?></td>
                        <td><?php echo htmlspecialchars($customer['username']); ?></td>
                        <td><?php echo htmlspecialchars($customer['email']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($customer['created_at'])); ?></td>
                        <td>
                            <?php 
                            $order_count = mysqli_fetch_assoc(mysqli_query($conn, 
                                "SELECT COUNT(*) as count FROM orders WHERE customer_id = " . $customer['id']))['count'];
                            echo $order_count;
                            ?>
                        </td>
                        <td>
                            <a href="admin_view_customer.php?id=<?php echo $customer['id']; ?>" class="action-btn view-btn">View</a>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this customer?');">
                                <input type="hidden" name="customer_id" value="<?php echo $customer['id']; ?>">
                                <button type="submit" name="delete_customer" class="action-btn delete-btn">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html> 