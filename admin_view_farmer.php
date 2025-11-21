<?php
require_once "includes/config.php";
session_start();

// Require admin session
if(!isset($_SESSION["user_id"]) || $_SESSION["user_type"] != "admin"){
    header("location: admin_login.php");
    exit;
}

// Validate id
$farmer_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if($farmer_id <= 0){
    http_response_code(404);
    echo "Invalid farmer id.";
    exit;
}

// Fetch farmer details
$farmer = null;
if($stmt = mysqli_prepare($conn, "SELECT id, username, email, user_type, created_at FROM users WHERE id = ? AND user_type = 'farmer'")){
    mysqli_stmt_bind_param($stmt, "i", $farmer_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $farmer = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

if(!$farmer){
    http_response_code(404);
    echo "Farmer not found.";
    exit;
}

// Fetch counts
$product_count = 0;
if($stmt = mysqli_prepare($conn, "SELECT COUNT(*) as c FROM products WHERE farmer_id = ?")){
    mysqli_stmt_bind_param($stmt, "i", $farmer_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    $product_count = $row ? intval($row['c']) : 0;
    mysqli_stmt_close($stmt);
}

// Fetch products
$products = [];
if($stmt = mysqli_prepare($conn, "SELECT id, name, price, category, stock, created_at FROM products WHERE farmer_id = ? ORDER BY created_at DESC")){
    mysqli_stmt_bind_param($stmt, "i", $farmer_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while($row = mysqli_fetch_assoc($res)){
        $products[] = $row;
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Details - Admin</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .admin-container { max-width: 1100px; margin: 80px auto 0; padding: 20px; }
        .back-btn { display: inline-block; padding: 10px 20px; background: #4CAF50; color: #fff; text-decoration: none; border-radius: 5px; margin-bottom: 20px; }
        .card { background: #fff; border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); padding: 20px; margin-bottom: 20px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; }
        .stat { background: #f6faf7; border: 1px solid #e1efe5; border-radius: 6px; padding: 16px; }
        .table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); border-radius: 5px; overflow: hidden; }
        .table th, .table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        .table th { background: #2196F3; color: #fff; }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script defer src="assets/js/main.js"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', Arial, sans-serif; }
    </style>
    <script>document.documentElement.classList.add('js')</script>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="admin-container">
        <a href="admin_manage_farmers.php" class="back-btn">Back to Farmers</a>

        <div class="card">
            <h1 style="margin: 0 0 10px;">Farmer #<?php echo $farmer['id']; ?> - <?php echo htmlspecialchars($farmer['username']); ?></h1>
            <div class="grid">
                <div class="stat">
                    <strong>Email</strong><br>
                    <?php echo htmlspecialchars($farmer['email']); ?>
                </div>
                <div class="stat">
                    <strong>Joined</strong><br>
                    <?php echo date('M d, Y', strtotime($farmer['created_at'])); ?>
                </div>
                <div class="stat">
                    <strong>Total Products</strong><br>
                    <?php echo $product_count; ?>
                </div>
            </div>
        </div>

        <div class="card">
            <h2 style="margin-top: 0;">Products</h2>
            <?php if(empty($products)): ?>
                <p>No products found for this farmer.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $p): ?>
                            <tr>
                                <td><?php echo $p['id']; ?></td>
                                <td><?php echo htmlspecialchars($p['name']); ?></td>
                                <td><?php echo htmlspecialchars((string)($p['category'] ?? '')); ?></td>
                                <td><?php echo isset($p['price']) ? number_format((float)$p['price'], 2) : '0.00'; ?></td>
                                <td><?php echo isset($p['stock']) ? intval($p['stock']) : 0; ?></td>
                                <td><?php echo isset($p['created_at']) ? date('M d, Y', strtotime($p['created_at'])) : ''; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>


