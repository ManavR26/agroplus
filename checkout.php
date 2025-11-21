<?php
require_once "includes/config.php";
session_start();

// Check if user is logged in and is a customer
if(!isset($_SESSION["user_id"]) || $_SESSION["user_type"] != "customer"){
    header("location: login.php");
    exit;
}

$success_message = '';
$order_details = null;

// Fetch cart items and calculate total
try {
    $sql = "SELECT ci.*, p.name, p.price 
            FROM cart_items ci 
            JOIN products p ON ci.product_id = p.id 
            WHERE ci.user_id = ?";
            
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt === false) {
        throw new Exception('Error preparing statement: ' . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Error executing statement: ' . mysqli_error($conn));
    }
    
    $cart_items = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    
    $total = 0;
    $items = [];
    while($item = mysqli_fetch_assoc($cart_items)) {
        $subtotal = $item['price'] * $item['quantity'];
        $total += $subtotal;
        $items[] = $item;
    }
    
} catch (Exception $e) {
    die('Error fetching cart items: ' . $e->getMessage());
}

// Handle payment submission
if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Create order
    $order_sql = "INSERT INTO orders (customer_id, total_amount, status) VALUES (?, ?, 'completed')";
    $stmt = mysqli_prepare($conn, $order_sql);
    mysqli_stmt_bind_param($stmt, "id", $_SESSION["user_id"], $total);
    
    if(mysqli_stmt_execute($stmt)) {
        $order_id = mysqli_insert_id($conn);
        
        // Add order items
        foreach($items as $item) {
            $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $item_sql);
            mysqli_stmt_bind_param($stmt, "iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
            mysqli_stmt_execute($stmt);
            
            // Update product stock
            $update_sql = "UPDATE products SET stock = stock - ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $update_sql);
            mysqli_stmt_bind_param($stmt, "ii", $item['quantity'], $item['product_id']);
            mysqli_stmt_execute($stmt);
        }
        
        // Clear cart
        $clear_sql = "DELETE FROM cart_items WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $clear_sql);
        mysqli_stmt_bind_param($stmt, "i", $_SESSION["user_id"]);
        mysqli_stmt_execute($stmt);
        
        // Show invoice
        $_SESSION['order_id'] = $order_id;
        $_SESSION['order_total'] = $total;
        $_SESSION['order_items'] = $items;
        header("Location: invoice.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - AgroPlus</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .checkout-container {
            max-width: 800px;
            margin: 100px auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .checkout-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }

        .checkout-header h1 {
            color: #2e7d32;
            margin-bottom: 10px;
        }

        .order-summary {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .total-amount {
            font-size: 1.2em;
            font-weight: bold;
            text-align: right;
            margin-top: 20px;
            color: #2e7d32;
        }

        .payment-button {
            background: #4CAF50;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
        }

        .payment-button:hover {
            background: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        @media (max-width: 768px) {
            .checkout-container {
                margin: 20px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="checkout-container">
        <div class="checkout-header">
            <h1>Checkout</h1>
            <p>Review your order and complete payment</p>
        </div>

        <div class="order-summary">
            <h2>Order Summary</h2>
            <?php foreach($items as $item): ?>
                <div class="order-item">
                    <span><?php echo htmlspecialchars($item['name']); ?> (×<?php echo $item['quantity']; ?>)</span>
                    <span>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                </div>
            <?php endforeach; ?>
            
            <div class="total-amount">
                Total: ₹<?php echo number_format($total, 2); ?>
            </div>
        </div>

        <button id="rzpPayBtn" class="payment-button">Pay Now ₹<?php echo number_format($total, 2); ?></button>
    </div>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        const payBtn = document.getElementById('rzpPayBtn');
        payBtn.addEventListener('click', async function(){
            payBtn.disabled = true;
            try {
                const createRes = await fetch('create_razorpay_order.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ amount: <?php echo json_encode((float)$total); ?> })
                });
                const orderData = await createRes.json();
                if(orderData.error){ throw new Error(orderData.error); }

                const options = {
                    key: orderData.key,
                    amount: Math.round(<?php echo json_encode((float)$total); ?> * 100),
                    currency: orderData.currency,
                    name: 'AgroPlus',
                    description: 'Order Payment',
                    handler: async function (response){
                        try{
                            const verifyRes = await fetch('verify_razorpay_payment.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({
                                    razorpay_payment_id: response.razorpay_payment_id,
                                    razorpay_signature: response.razorpay_signature || '',
                                    local_order_id: orderData.local_order_id
                                })
                            });
                            const verify = await verifyRes.json();
                            if(verify.success && verify.redirect){
                                window.location.href = verify.redirect;
                            } else {
                                alert(verify.message || 'Payment verification failed');
                            }
                        }catch(e){
                            alert(e.message);
                        }
                    },
                    prefill: {
                        name: <?php echo json_encode($_SESSION['username'] ?? ''); ?>,
                        email: <?php echo json_encode($_SESSION['email'] ?? ''); ?>
                    },
                    theme: { color: '#2e7d32' }
                };
                const rzp = new Razorpay(options);
                rzp.open();
            } catch (e) {
                alert(e.message);
            } finally {
                payBtn.disabled = false;
            }
        });
    </script>
</body>
</html> 