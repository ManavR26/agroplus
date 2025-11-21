<?php
require_once "includes/config.php";
session_start();

// Check if user is logged in and is an admin
if(!isset($_SESSION["user_id"]) || $_SESSION["user_type"] != "admin"){
    header("location: admin_login.php");
    exit;
}

// Handle farmer deletion
if(isset($_POST['delete_farmer'])) {
    $farmer_id = $_POST['farmer_id'];
    mysqli_query($conn, "DELETE FROM users WHERE id = $farmer_id AND user_type = 'farmer'");
}

// Fetch all farmers
$sql = "SELECT * FROM users WHERE user_type = 'farmer' ORDER BY created_at DESC";
$farmers = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Farmers - Admin Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 80px auto 0;
            padding: 20px;
        }

        .farmers-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border-radius: 5px;
            overflow: hidden;
        }

        .farmers-table th,
        .farmers-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .farmers-table th {
            background: #4CAF50;
            color: white;
        }

        .farmers-table tr:hover {
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
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="admin-container">
        <a href="admin_dashboard.php" class="back-btn">Back to Dashboard</a>
        
        <h1>Manage Farmers</h1>

        <table class="farmers-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Join Date</th>
                    <th>Products</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($farmer = mysqli_fetch_assoc($farmers)): ?>
                    <tr>
                        <td><?php echo $farmer['id']; ?></td>
                        <td><?php echo htmlspecialchars($farmer['username']); ?></td>
                        <td><?php echo htmlspecialchars($farmer['email']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($farmer['created_at'])); ?></td>
                        <td>
                            <?php 
                            $product_count = mysqli_fetch_assoc(mysqli_query($conn, 
                                "SELECT COUNT(*) as count FROM products WHERE farmer_id = " . $farmer['id']))['count'];
                            echo $product_count;
                            ?>
                        </td>
                        <td>
                            <a href="admin_view_farmer.php?id=<?php echo $farmer['id']; ?>" class="action-btn view-btn">View</a>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this farmer?');">
                                <input type="hidden" name="farmer_id" value="<?php echo $farmer['id']; ?>">
                                <button type="submit" name="delete_farmer" class="action-btn delete-btn">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html> 