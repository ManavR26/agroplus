<?php
require_once "config.php";
session_start();

header('Content-Type: application/json');

try {
    $sql = "SELECT * FROM products WHERE stock > 0";
    $result = mysqli_query($conn, $sql);
    
    $products = [];
    while($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    
    echo json_encode($products);
} catch (Exception $e) {
    echo json_encode(['error' => 'Failed to fetch products']);
} 