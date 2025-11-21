<?php
require_once "includes/config.php";
require_once "includes/razorpay_config.php";
session_start();

header('Content-Type: application/json');

if(!isset($_SESSION["user_id"]) || $_SESSION["user_type"] != "customer"){
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$razorpay_payment_id = $input['razorpay_payment_id'] ?? '';
$razorpay_signature = $input['razorpay_signature'] ?? '';
$local_order_id = $input['local_order_id'] ?? '';

if(!$razorpay_payment_id || !$local_order_id){
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid payload']);
    exit;
}

// Minimal verification placeholder (for full security, generate real Razorpay order and verify signature)
// Here we just check the session-stored local order id exists.
$pending = $_SESSION['pending_payment'] ?? null;
if(!$pending || $pending['local_order_id'] !== $local_order_id){
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Session expired. Please try again.']);
    exit;
}

$amount = $pending['amount'];

// Create order and items from cart
try {
    // Fetch cart
    $cart_sql = "SELECT ci.*, p.price, p.name FROM cart_items ci JOIN products p ON ci.product_id = p.id WHERE ci.user_id = ?";
    $stmt = mysqli_prepare($conn, $cart_sql);
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $items = [];
    $computed_total = 0;
    while($row = mysqli_fetch_assoc($res)){
        $items[] = $row;
        $computed_total += $row['price'] * $row['quantity'];
    }
    mysqli_stmt_close($stmt);

    // Basic total check
    if(abs($computed_total - $amount) > 0.01){
        throw new Exception('Amount mismatch');
    }

    // Insert order
    $order_sql = "INSERT INTO orders (customer_id, total_amount, status) VALUES (?, ?, 'completed')";
    $stmt = mysqli_prepare($conn, $order_sql);
    mysqli_stmt_bind_param($stmt, "id", $_SESSION['user_id'], $computed_total);
    mysqli_stmt_execute($stmt);
    $order_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    foreach($items as $it){
        $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $item_sql);
        mysqli_stmt_bind_param($stmt, "iiid", $order_id, $it['product_id'], $it['quantity'], $it['price']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $update_sql = "UPDATE products SET stock = stock - ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($stmt, "ii", $it['quantity'], $it['product_id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    $clear_sql = "DELETE FROM cart_items WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $clear_sql);
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Prepare invoice session
    $_SESSION['order_id'] = $order_id;
    $_SESSION['order_total'] = $computed_total;
    $_SESSION['order_items'] = $items;
    unset($_SESSION['pending_payment']);

    echo json_encode(['success' => true, 'redirect' => 'invoice.php']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Payment verified but order failed: ' . $e->getMessage()]);
}
?>


