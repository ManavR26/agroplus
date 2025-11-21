<?php
require_once "config.php";
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

if ($_SESSION['user_type'] != 'customer') {
    echo json_encode(['success' => false, 'message' => 'Only customers can add to cart']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$product_id = $data['product_id'] ?? null;
$add_quantity = isset($data['quantity']) && intval($data['quantity']) > 0 ? intval($data['quantity']) : 1;

if (!$product_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid product']);
    exit;
}

// Check if product exists and has stock
$sql = "SELECT stock FROM products WHERE id = ? AND stock > 0";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    echo json_encode(['success' => false, 'message' => 'Product not available']);
    exit;
}

// Check if this product already exists in the user's cart
$check_sql = "SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?";
$stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($stmt, "ii", $_SESSION['user_id'], $product_id);
mysqli_stmt_execute($stmt);
$existing = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($existing)) {
    // Update existing quantity (increment)
    $new_qty = $row['quantity'] + $add_quantity;
    $update_sql = "UPDATE cart_items SET quantity = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($stmt, "ii", $new_qty, $row['id']);
    mysqli_stmt_execute($stmt);
} else {
    // Insert new cart item
    $insert_sql = "INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insert_sql);
    mysqli_stmt_bind_param($stmt, "iii", $_SESSION['user_id'], $product_id, $add_quantity);
    mysqli_stmt_execute($stmt);
}

echo json_encode(['success' => true, 'message' => 'Added to cart']);