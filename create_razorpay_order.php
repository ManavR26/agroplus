<?php
require_once "includes/config.php";
require_once "includes/razorpay_config.php";
session_start();

header('Content-Type: application/json');

if(!isset($_SESSION["user_id"]) || $_SESSION["user_type"] != "customer"){
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Expect JSON: { amount: number }
$input = json_decode(file_get_contents('php://input'), true);
$amount = isset($input['amount']) ? floatval($input['amount']) : 0;
if($amount <= 0){
    http_response_code(400);
    echo json_encode(['error' => 'Invalid amount']);
    exit;
}

// Create a pseudo order id for client display; real payment verification will be done server-side
// Normally you would call Razorpay Orders API here using their PHP SDK.
// To keep it dependency-free, we create a local token and pass details to client.
$local_order_id = 'local_' . time() . '_' . bin2hex(random_bytes(4));

$_SESSION['pending_payment'] = [
    'local_order_id' => $local_order_id,
    'amount' => $amount,
];

echo json_encode([
    'key' => RAZORPAY_KEY_ID,
    'currency' => RAZORPAY_CURRENCY,
    'local_order_id' => $local_order_id
]);
?>


