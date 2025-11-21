<?php
require_once "includes/config.php";
session_start();

// Prevent caching to avoid viewing after logout via back button
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP/1.1
header('Pragma: no-cache'); // HTTP/1.0
header('Expires: 0'); // Proxies

// Check if user is logged in and is a customer
if(!isset($_SESSION["user_id"]) || $_SESSION["user_type"] != "customer"){
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - AgroPlus</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .categories-container {
            max-width: 1200px;
            margin: 80px auto 0;
            padding: 20px;
        }

        .categories-header {
            text-align: center;
            margin-bottom: 40px;
            color: #2e7d32;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            padding: 20px;
        }

        .category-card {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .category-card:hover {
            transform: translateY(-5px);
        }

        .category-image {
            width: 100%;
            height: 200px;
            background-size: cover;
            background-position: center;
        }

        .category-image--img {
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .category-image--img img {
            width: 100%;
            height: 100%;
            object-fit: contain; /* show full image without cropping */
        }

        .category-content {
            padding: 20px;
        }

        .category-title {
            color: #2e7d32;
            margin-bottom: 10px;
        }

        .category-description {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .category-link {
            display: inline-block;
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .category-link:hover {
            background: #45a049;
        }

        /* Category-specific background images */
        .browse-products {
            background-image: url('assets/images/products.jpg');
        }

        .view-cart {
            background-image: url('assets/images/cart.jpg');
        }

        .order-history {
            background-image: url('assets/images/orders.jpg');
        }

        .waste-management {
            background-image: url('assets/images/waste-management.jpg');
        }

        .organic-methods {
            background-image: url('assets/images/organic-methods.jpg');
        }

        .government-schemes {
            background-image: url('assets/images/government-schemes.jpg');
        }
    </style>
    <!-- Add this meta tag to prevent caching -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <!-- Add this script to handle browser back button -->
    <script>
        window.onpageshow = function(event) {
            if (event.persisted) {
                window.location.replace('logout.php');
            }
        };

        // If navigated via back/forward, force logout to home
        if (window.performance && window.performance.navigation.type === 2) {
            window.location.replace('logout.php');
        }
    </script>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="categories-container">
        <div class="categories-header">
            <h1>Welcome to AgroPlus</h1>
            <p>Explore our agricultural marketplace and sustainable farming practices</p>
        </div>

        <div class="categories-grid">
            <!-- Browse Products Card -->
            <div class="category-card">
                <div class="category-image category-image--img"><img src="assets/images/12139335.png" alt="Browse Products"></div>
                <div class="category-content">
                    <h2 class="category-title">Browse Products</h2>
                    <p class="category-description">
                        Explore a wide range of agricultural products from local farmers. Find fresh produce, organic items, and more.
                    </p>
                    <a href="products.php" class="category-link">Browse Now</a>
                </div>
            </div>

            <!-- Shopping Cart Card -->
            <div class="category-card">
                <div class="category-image category-image--img"><img src="assets/images/728606f7599db67e35ecaa539070ba84.jpg" alt="Shopping Cart"></div>
                <div class="category-content">
                    <h2 class="category-title">Shopping Cart</h2>
                    <p class="category-description">
                        View your selected items, manage quantities, and proceed to checkout.
                    </p>
                    <a href="cart.php" class="category-link">View Cart</a>
                </div>
            </div>

            <!-- Order History Card -->
            <div class="category-card">
                <div class="category-image category-image--img"><img src="assets/images/download.png" alt="Order History"></div>
                <div class="category-content">
                    <h2 class="category-title">Order History</h2>
                    <p class="category-description">
                        Track your orders, view past purchases, and manage deliveries.
                    </p>
                    <a href="order_history.php" class="category-link">View Orders</a>
                </div>
            </div>

            <!-- Government Schemes Card -->
            <div class="category-card">
                <div class="category-image category-image--img"><img src="assets/images/download (1).png" alt="Government Schemes"></div>
                <div class="category-content">
                    <h2 class="category-title">Government Schemes</h2>
                    <p class="category-description">
                        Explore various government schemes available for farmers to enhance productivity and income.
                    </p>
                    <a href="government_schemes.php" class="category-link">Learn More</a>
                </div>
            </div>

            <!-- Waste Management Card -->
            <div class="category-card">
                <div class="category-image category-image--img"><img src="assets/images/download (2).png" alt="Waste Management"></div>
                <div class="category-content">
                    <h2 class="category-title">Waste Management</h2>
                    <p class="category-description">
                        Learn about efficient agricultural waste management techniques and sustainable farming practices.
                    </p>
                    <a href="waste_management.php" class="category-link">Learn More</a>
                </div>
            </div>

            <!-- Organic Methods Card -->
            <div class="category-card">
                <div class="category-image category-image--img"><img src="assets/images/download (3).png" alt="Organic Methods"></div>
                <div class="category-content">
                    <h2 class="category-title">Organic Methods</h2>
                    <p class="category-description">
                        Discover organic farming methods, natural pesticides, and eco-friendly agricultural practices.
                    </p>
                    <a href="customer_organic_methods.php" class="category-link">Learn More</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 