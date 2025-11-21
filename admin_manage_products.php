<?php
require_once "includes/config.php";
session_start();

// Check if user is logged in and is an admin
if(!isset($_SESSION["user_id"]) || $_SESSION["user_type"] != "admin"){
    header("location: admin_login.php");
    exit;
}

$success = $error = "";

// Handle product deletion
if(isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];
    $delete_sql = "DELETE FROM products WHERE id = ?";
    $stmt = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    
    if(mysqli_stmt_execute($stmt)){
        $success = "Product deleted successfully!";
    } else {
        $error = "Error deleting product: " . mysqli_error($conn);
    }
}

// Fetch all products
$sql = "SELECT p.*, u.username as farmer_name 
        FROM products p 
        LEFT JOIN users u ON p.farmer_id = u.id 
        ORDER BY p.created_at DESC";
$products = mysqli_query($conn, $sql);

// Check if the query was successful
if (!$products) {
    error_log("Database query failed: " . mysqli_error($conn));
    die("Unable to load products. Please try again later.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 80px auto 0;
            padding: 20px;
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

        .products-table tr:hover {
            background: #f5f5f5;
        }

        .action-btn {
            padding: 6px 12px;
            border-radius: 3px;
            text-decoration: none;
            color: white;
            font-size: 14px;
            cursor: pointer;
            border: none;
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

        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="admin-container">
        <div class="header">
            <h1>Manage Products</h1>
            <a href="admin_dashboard.php" class="back-btn">Back to Dashboard</a>
        </div>

        <table class="products-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Farmer</th>
                    <th>Added Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($product = mysqli_fetch_assoc($products)): ?>
                    <tr>
                        <td>
                            <?php 
                            $stored = (string)$product['image'];
                            $src = '';
                            if($stored !== ''){
                                if(strpos($stored, 'assets/') === 0 || strpos($stored, 'uploads/') === 0){
                                    $src = $stored; // already a relative path
                                } else {
                                    if(file_exists('assets/images/products/' . $stored)){
                                        $src = 'assets/images/products/' . $stored;
                                    } elseif(file_exists('uploads/products/' . $stored)){
                                        $src = 'uploads/products/' . $stored;
                                    }
                                }
                            }
                            if($src === ''){ $src = 'assets/images/field.jpg'; }
                            ?>
                            <img src="<?php echo htmlspecialchars($src); ?>" 
                                 alt="Product Image" 
                                 class="product-image">
                        </td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td>₹<?php echo htmlspecialchars($product['price']); ?></td>
                        <td><?php echo htmlspecialchars($product['stock']); ?></td>
                        <td><?php echo htmlspecialchars($product['farmer_name']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($product['created_at'])); ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <button type="submit" 
                                        name="delete_product" 
                                        class="action-btn delete-btn"
                                        onclick="return confirm('Are you sure you want to delete this product?');">
                                    Delete
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