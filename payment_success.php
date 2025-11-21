<?php
require_once "includes/config.php";
session_start();

if(!isset($_SESSION["user_id"]) || !isset($_GET['order_id'])){
    header("location: index.php");
    exit;
}

$order_id = intval($_GET['order_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success - AgroPlus</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .success-container {
            max-width: 600px;
            margin: 100px auto;
            text-align: center;
            padding: 40px 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .success-icon {
            color: #4CAF50;
            font-size: 64px;
            margin-bottom: 20px;
        }
        .success-message {
            color: #2e7d32;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .order-number {
            color: #666;
            margin-bottom: 30px;
        }
        .continue-shopping {
            display: inline-block;
            padding: 12px 24px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .continue-shopping:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="success-container">
        <div class="success-icon">✓</div>
        <h1 class="success-message">Payment Successful!</h1>
        <p class="order-number">Order #<?php echo $order_id; ?></p>
        <p>Thank you for your purchase. Your order has been confirmed.</p>
        <p>You will receive an email confirmation shortly.</p>
        <a href="products.php" class="continue-shopping">Continue Shopping</a>
    </div>
</body>
</html> 