<?php
require_once "includes/config.php";
session_start();

// Check if user is logged in and is a farmer
if(!isset($_SESSION["user_id"]) || $_SESSION["user_type"] != "farmer"){
    header("location: login.php");
    exit;
}

// Get farmer's products
$products = [];
$sql = "SELECT * FROM products WHERE farmer_id = ? ORDER BY created_at DESC";
if($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $_SESSION["user_id"]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Products - AgroPlus</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .products-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
        }
        .products-header {
            text-align: center;
            color: #2e7d32;
            margin-bottom: 30px;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            padding: 20px;
        }
        .product-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
        }
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .product-details {
            padding: 15px;
        }
        .product-name {
            font-size: 1.2em;
            color: #333;
            margin-bottom: 10px;
        }
        .product-category {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 5px;
        }
        .product-price {
            color: #4CAF50;
            font-size: 1.3em;
            font-weight: bold;
            margin: 10px 0;
        }
        .product-description {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 10px;
        }
        .add-product-btn {
            display: inline-block;
            background: #4CAF50;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .add-product-btn:hover {
            background: #45a049;
        }
        .no-products {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #4CAF50;
            padding: 1rem 2rem;
            color: white;
        }
        .navbar-brand {
            font-size: 24px;
            font-weight: bold;
        }
        .navbar-brand a {
            color: white;
            text-decoration: none;
        }
        .nav-links {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            align-items: center;
        }
        .nav-links li {
            margin-left: 20px;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .nav-links a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="products-container">
        <h1 class="products-header">My Products</h1>
        
        <div style="text-align: center;">
            <a href="farmer_dashboard.php" class="add-product-btn">+ Add New Product</a>
        </div>

        <?php if(empty($products)): ?>
            <div class="no-products">
                <h2>No products added yet</h2>
                <p>Start adding your products to showcase them to customers!</p>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach($products as $product): ?>
                    <div class="product-card">
                        <img class="product-image" 
                             src="<?php echo !empty($product['image']) ? htmlspecialchars($product['image']) : 'assets/images/default-product.jpg'; ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="product-details">
                            <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <div class="product-category">Category: <?php echo htmlspecialchars($product['category']); ?></div>
                            <div class="product-price">₹<?php echo htmlspecialchars($product['price']); ?></div>
                            <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                            <div>Stock: <?php echo htmlspecialchars($product['stock']); ?> units</div>
                            <?php if($product['is_organic']): ?>
                                <div style="color: #4CAF50; margin-top: 5px;">✓ Organic Product</div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 