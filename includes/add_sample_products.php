<?php
require_once "config.php";

// First, get a farmer ID (assuming you have registered at least one farmer)
$farmer_sql = "SELECT id FROM users WHERE user_type = 'farmer' LIMIT 1";
$farmer_result = mysqli_query($conn, $farmer_sql);
$farmer = mysqli_fetch_assoc($farmer_result);

if (!$farmer) {
    die("Please register a farmer first!");
}

$farmer_id = $farmer['id'];

// Sample products
$products = [
    [
        'name' => 'Organic Rice',
        'description' => 'Fresh organic rice from local farms',
        'price' => 75.00,
        'category' => 'Grains',
        'is_organic' => 1,
        'stock' => 100
    ],
    [
        'name' => 'Fresh Tomatoes',
        'description' => 'Locally grown fresh tomatoes',
        'price' => 40.00,
        'category' => 'Vegetables',
        'is_organic' => 0,
        'stock' => 50
    ],
    [
        'name' => 'Organic Wheat',
        'description' => 'Premium quality organic wheat',
        'price' => 60.00,
        'category' => 'Grains',
        'is_organic' => 1,
        'stock' => 75
    ],
    [
        'name' => 'Fresh Potatoes',
        'description' => 'Farm fresh potatoes',
        'price' => 30.00,
        'category' => 'Vegetables',
        'is_organic' => 0,
        'stock' => 200
    ]
];

// Insert products
$sql = "INSERT INTO products (farmer_id, name, description, price, category, is_organic, stock) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);

foreach ($products as $product) {
    mysqli_stmt_bind_param($stmt, "issdsis", 
        $farmer_id,
        $product['name'],
        $product['description'],
        $product['price'],
        $product['category'],
        $product['is_organic'],
        $product['stock']
    );
    
    if(mysqli_stmt_execute($stmt)) {
        echo "Added product: " . $product['name'] . "<br>";
    } else {
        echo "Error adding product: " . $product['name'] . "<br>";
    }
}

mysqli_stmt_close($stmt);
echo "Sample products have been added!";
?> 