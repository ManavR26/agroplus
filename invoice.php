<?php
require_once "includes/config.php";
session_start();

if(!isset($_SESSION["user_id"]) || !isset($_SESSION['order_id'])){
    header("location: index.php");
    exit;
}

$order_id = $_SESSION['order_id'];
$total = $_SESSION['order_total'];
$items = $_SESSION['order_items'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - AgroPlus</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .invoice-container {
            max-width: 800px;
            margin: 100px auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .success-message {
            text-align: center;
            color: #2e7d32;
            margin-bottom: 30px;
        }

        .success-message h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .invoice-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }

        .invoice-details {
            margin-bottom: 30px;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .invoice-table th, .invoice-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .invoice-table th {
            background: #f9f9f9;
        }

        .total-row {
            font-weight: bold;
            color: #2e7d32;
        }

        .print-button {
            background: #4CAF50;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-right: 10px;
            transition: all 0.3s ease;
        }

        .print-button:hover {
            background: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .continue-shopping {
            display: inline-block;
            background: #2196F3;
            color: white;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .continue-shopping:hover {
            background: #1976D2;
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        @media print {
            .no-print {
                display: none;
            }
            .invoice-container {
                margin: 0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="invoice-container">
        <div class="success-message">
            <h1>Payment Successful!</h1>
            <p>Your order has been confirmed</p>
        </div>

        <div class="invoice-header">
            <h2>Invoice</h2>
            <p>Order #<?php echo $order_id; ?></p>
            <p>Date: <?php echo date('d M Y'); ?></p>
        </div>

        <div class="invoice-details">
            <p><strong>Customer:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?></p>
        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>₹<?php echo number_format($item['price'], 2); ?></td>
                        <td>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;">Total:</td>
                    <td>₹<?php echo number_format($total, 2); ?></td>
                </tr>
            </tbody>
        </table>

        <div class="no-print" style="text-align: center;">
            <button onclick="window.print()" class="print-button">Print Invoice</button>
            <a href="customer_dashboard.php" class="continue-shopping">Back to Dashboard</a>
        </div>
    </div>

    <?php
    // Clear the session variables after displaying the invoice
    unset($_SESSION['order_id']);
    unset($_SESSION['order_total']);
    unset($_SESSION['order_items']);
    ?>

    <script>
        // Prevent going back to checkout page
        window.history.pushState(null, null, window.location.href);
        window.onpopstate = function() {
            window.location.href = 'customer_dashboard.php';
        };
    </script>
</body>
</html> 