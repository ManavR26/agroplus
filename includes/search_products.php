<?php
require_once "config.php";
session_start();

header('Content-Type: application/json');

$searchTerm = $_GET['term'] ?? '';

try {
    // Use prepared statements instead of string concatenation
    $sql = "SELECT * FROM products 
            WHERE (name LIKE ? OR description LIKE ?) 
            AND stock > 0";
    
    $stmt = mysqli_prepare($conn, $sql);
    if($stmt) {
        $searchParam = '%' . $searchTerm . '%';
        mysqli_stmt_bind_param($stmt, "ss", $searchParam, $searchParam);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $products = [];
        while($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
        
        mysqli_stmt_close($stmt);
        echo json_encode($products);
    } else {
        echo json_encode(['error' => 'Failed to search products']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Failed to search products']);
} 