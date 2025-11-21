<?php
require_once "includes/config.php";
session_start();

// Check if user is logged in and is a farmer
if(!isset($_SESSION["user_id"]) || $_SESSION["user_type"] != "farmer"){
    header("location: login.php");
    exit;
}

$success_message = $error_message = "";

// Handle product upload
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $description = trim($_POST["description"]);
    $price = floatval($_POST["price"]);
    $category = trim($_POST["category"]);
    $stock = intval($_POST["stock"]);
    $is_organic = isset($_POST["is_organic"]) ? 1 : 0;
    
    // Handle image upload
    $image_path = "";
    if(isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $allowed = ["jpg" => "image/jpg", "jpeg" => "image/jpeg", "png" => "image/png"];
        $filename = $_FILES["image"]["name"];
        $filetype = $_FILES["image"]["type"];
        $filesize = $_FILES["image"]["size"];
        
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if(!array_key_exists($ext, $allowed)) {
            $error_message = "Error: Please select a valid image format.";
        }
        
        if($filesize > 5242880) {
            $error_message = "Error: File size is too large. Max 5MB allowed.";
        }
        
        if(empty($error_message)) {
            $new_filename = uniqid() . "." . $ext;
            $image_path = "assets/images/products/" . $new_filename;
            
            // Create directory if it doesn't exist
            if(!file_exists("assets/images/products")) {
                mkdir("assets/images/products", 0777, true);
            }
            
            move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
        }
    }
    
    if(empty($error_message)) {
        $sql = "INSERT INTO products (farmer_id, name, description, price, category, is_organic, stock, image) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        if($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "issdsiis", 
                $_SESSION["user_id"],
                $name,
                $description,
                $price,
                $category,
                $is_organic,
                $stock,
                $image_path
            );
            
            if(mysqli_stmt_execute($stmt)) {
                header("Location: farmer_dashboard.php?success=1");
                exit;
            } else {
                $error_message = "Error adding product. Please try again.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Get farmer's products
$products = [];
$sql = "SELECT * FROM products WHERE farmer_id = ?";
if($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $_SESSION["user_id"]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - AgroPlus</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .product-form {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
            border-radius: 8px;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .success {
            background: #dff0d8;
            color: #3c763d;
        }
        .error {
            background: #f2dede;
            color: #a94442;
        }
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .products-table th, .products-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .products-table th {
            background: #4CAF50;
            color: white;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="product-form">
        <h2>Add New Product</h2>
        
        <?php if(!empty($success_message)): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if(!empty($error_message)): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="name" required>
            </div>
            
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" required></textarea>
            </div>
            
            <div class="form-group">
                <label>Price (₹)</label>
                <input type="number" name="price" step="0.01" required>
            </div>
            
            <div class="form-group">
                <label>Category</label>
                <select name="category" required>
                    <option value="Vegetables">Vegetables</option>
                    <option value="Fruits">Fruits</option>
                    <option value="Grains">Grains</option>
                    <option value="Dairy">Dairy</option>
                    <option value="Others">Others</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Stock Quantity</label>
                <input type="number" name="stock" required>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_organic">
                    Organic Product
                </label>
            </div>
            
            <div class="form-group">
                <label>Product Image</label>
                <input type="file" name="image" accept="image/*" required>
            </div>
            
            <button type="submit" class="btn">Add Product</button>
        </form>
    </div>

    <div class="container">
        <h2>My Products</h2>
        <table class="products-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Category</th>
                    <th>Organic</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($products as $product): ?>
                    <tr>
                        <td>
                            <img src="<?php echo !empty($product['image']) ? htmlspecialchars($product['image']) : 'assets/images/default-product.jpg'; ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 style="width: 50px; height: 50px; object-fit: cover;">
                        </td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td>₹<?php echo htmlspecialchars($product['price']); ?></td>
                        <td><?php echo htmlspecialchars($product['stock']); ?></td>
                        <td><?php echo htmlspecialchars($product['category']); ?></td>
                        <td><?php echo $product['is_organic'] ? 'Yes' : 'No'; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html> 