<?php
require_once "includes/config.php";
session_start();

if(!isset($_SESSION["user_id"]) || $_SESSION["user_type"] != "customer"){
    header("location: login.php");
    exit;
}

// Clear old cart items first
$clear_old_sql = "DELETE FROM cart_items WHERE user_id = ? AND created_at < NOW() - INTERVAL 24 HOUR";
$stmt = mysqli_prepare($conn, $clear_old_sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);

// Handle quantity updates
if(isset($_POST['action'])) {
    $cart_item_id = intval($_POST['cart_item_id']);
    
    if($_POST['action'] === 'remove') {
        // Remove item completely
        $sql = "DELETE FROM cart_items WHERE id = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt === false) {
            die('Error preparing delete statement: ' . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt, "ii", $cart_item_id, $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else if($_POST['action'] === 'decrease') {
        // Decrease quantity by 1
        $sql = "UPDATE cart_items SET quantity = quantity - 1 WHERE id = ? AND user_id = ? AND quantity > 1";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt === false) {
            die('Error preparing decrease statement: ' . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt, "ii", $cart_item_id, $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else if($_POST['action'] === 'increase') {
        // Increase quantity by 1
        $sql = "UPDATE cart_items SET quantity = quantity + 1 WHERE id = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt === false) {
            die('Error preparing increase statement: ' . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt, "ii", $cart_item_id, $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    
    header("Location: cart.php");
    exit;
}

// Handle bulk quantity update (single submit for multiple items)
if (isset($_POST['bulk_update']) && isset($_POST['items']) && is_array($_POST['items'])) {
    foreach ($_POST['items'] as $id => $qty) {
        $cart_item_id = intval($id);
        $quantity = intval($qty);
        if ($quantity <= 0) {
            // Remove if set to 0 or negative
            $sql = "DELETE FROM cart_items WHERE id = ? AND user_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ii", $cart_item_id, $_SESSION['user_id']);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        } else {
            // Update to the specified absolute quantity
            $sql = "UPDATE cart_items SET quantity = ? WHERE id = ? AND user_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "iii", $quantity, $cart_item_id, $_SESSION['user_id']);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
    }
    header("Location: cart.php");
    exit;
}

// Clear any old session cart data
if(isset($_SESSION['cart'])) {
    unset($_SESSION['cart']);
}

// Fetch cart items from database
try {
    $sql = "SELECT ci.*, p.name, p.price, p.image 
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
    
} catch (Exception $e) {
    die('Error fetching cart items: ' . $e->getMessage());
}

$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - AgroPlus</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .cart-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: relative;
            top: 100px;
        }

        .cart-item {
            display: flex;
            align-items: center;
            padding: 20px;
            background: #f9f9f9;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .cart-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 20px;
        }

        .item-details {
            flex: 1;
        }

        .item-details h3 {
            color: #2e7d32;
            margin: 0 0 10px 0;
        }

        .item-details p {
            color: #666;
            margin: 5px 0;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 15px;
        }

        .quantity-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .decrease-btn {
            background: #ff9800;
            color: white;
        }

        .increase-btn {
            background: #4CAF50;
            color: white;
        }

        .remove-btn {
            background: #f44336;
            color: white;
        }

        .quantity-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .total-section {
            text-align: right;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
            margin-top: 20px;
        }

        .total-section h2 {
            color: #2e7d32;
            margin-bottom: 15px;
        }

        .checkout-btn {
            display: inline-block;
            background: #4CAF50;
            color: white;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .checkout-btn:hover {
            background: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .empty-cart {
            text-align: center;
            padding: 40px 20px;
            background: #f9f9f9;
            border-radius: 8px;
            margin: 20px 0;
        }

        .empty-cart h2 {
            color: #2e7d32;
            margin-bottom: 15px;
        }

        .empty-cart p {
            color: #666;
            margin-bottom: 25px;
        }

        .continue-shopping {
            display: inline-block;
            background: #4CAF50;
            color: white;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .continue-shopping:hover {
            background: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        @media (max-width: 768px) {
            .cart-container {
                margin: 0 20px;
                padding: 20px;
                top: 80px;
            }

            .cart-item {
                flex-direction: column;
                text-align: center;
            }

            .cart-item img {
                margin: 0 0 15px 0;
            }

            .quantity-controls {
                justify-content: center;
            }

            .total-section {
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="cart-container">
        <div class="cart-header">
            <h1>Shopping Cart</h1>
        </div>

        <?php if(mysqli_num_rows($cart_items) > 0): ?>
            <form method="POST">
                <input type="hidden" name="bulk_update" value="1">
            <?php while($item = mysqli_fetch_assoc($cart_items)):
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
            ?>
                <div class="cart-item">
                    <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                         alt="<?php echo htmlspecialchars($item['name']); ?>">
                    
                    <div class="item-details">
                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p>Price: ₹<?php echo number_format($item['price'], 2); ?></p>
                        <p>Subtotal: ₹<?php echo number_format($subtotal, 2); ?></p>
                        
                        <div class="quantity-controls">
                            <label for="qty-<?php echo $item['id']; ?>">Quantity</label>
                            <input id="qty-<?php echo $item['id']; ?>" type="number" min="0" name="items[<?php echo $item['id']; ?>]" value="<?php echo $item['quantity']; ?>" style="width:80px;">
                            <button type="submit" class="quantity-btn remove-btn" name="items[<?php echo $item['id']; ?>]" value="0">Remove</button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
                <div class="total-section">
                    <h2>Total: ₹<?php echo number_format($total, 2); ?></h2>
                    <button type="submit" class="checkout-btn" style="margin-right:10px;">Update Cart</button>
                    <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
                </div>
            </form>
        <?php else: ?>
            <div class="empty-cart">
                <h2>Your cart is empty</h2>
                <p>Add some products to your cart and they will appear here</p>
                <a href="products.php" class="continue-shopping">Continue Shopping</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Add smooth transitions for buttons
        document.querySelectorAll('.continue-shopping').forEach(button => {
            button.addEventListener('mouseover', function() {
                this.style.transition = 'all 0.3s ease';
            });
        });
    </script>
</body>
</html> 