<?php
require_once "includes/config.php";
// Start session properly
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Prevent caching to avoid viewing after logout via back button
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Debug session info
error_log("Categories.php - User ID: " . (isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : 'not set'));
error_log("Categories.php - User Type: " . (isset($_SESSION["user_type"]) ? $_SESSION["user_type"] : 'not set'));

// Check if user is logged in and is a farmer
if(!isset($_SESSION["user_id"]) || $_SESSION["user_type"] != "farmer"){
    header("location: login.php");
    exit;
}

// Update last activity to prevent session timeout
$_SESSION['last_activity'] = time();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories - AgroPlus</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
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
            grid-template-columns: repeat(3, 1fr);
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
            object-fit: contain;
        }

        .category-content {
            padding: 20px;
        }

        .category-title {
            font-size: 24px;
            color: #2e7d32;
            margin-bottom: 10px;
            font-weight: bold;
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
            background: #2e7d32;
        }

        /* Category-specific background images */
        .upload-product {
            background-image: url('assets/images/upload-product.jpg');
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
</head>
<body>
    <script>
        // If navigated via back/forward cache or history, force logout to home
        window.onpageshow = function(event) {
            if (event.persisted) {
                window.location.replace('logout.php');
            }
        };
        if (window.performance && window.performance.navigation && window.performance.navigation.type === 2) {
            window.location.replace('logout.php');
        }
    </script>
    <?php include 'navbar.php'; ?>

    <div class="categories-container">
        <div class="categories-header">
            <h1>Farmer Services</h1>
            <p>Choose from our range of agricultural services</p>
        </div>

        <div class="categories-grid">
            <!-- Upload Product Card -->
            <div class="category-card">
                <div class="category-image category-image--img"><img src="assets/images/download (4).png" alt="Upload Product"></div>
                <div class="category-content">
                    <h2 class="category-title">Upload Product</h2>
                    <p class="category-description">
                        List your agricultural products for sale. Reach customers directly and get the best price for your produce.
                    </p>
                    <a href="upload_product.php" class="category-link">Upload Now</a>
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
                    <a href="organic_methods.php" class="category-link">Explore</a>
                </div>
            </div>

            <!-- Government Schemes Card -->
            <div class="category-card">
                <div class="category-image category-image--img"><img src="assets/images/download (1).png" alt="Government Schemes"></div>
                <div class="category-content">
                    <h2 class="category-title">Government Schemes</h2>
                    <p class="category-description">
                        Learn about various government schemes and benefits available for farmers to enhance productivity.
                    </p>
                    <a href="government_schemes.php" class="category-link">Learn More</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 