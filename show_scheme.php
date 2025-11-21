<?php
require_once "includes/config.php";
session_start();

// Strict admin check
if(!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "admin"){
    header("location: admin_login.php");
    exit;
}

// Fetch the scheme to show
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitize ID input
    $query = "SELECT * FROM government_schemes WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    if($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $scheme = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        if(!$scheme) {
            header("Location: admin_government_schemes.php");
            exit;
        }
    } else {
        header("Location: admin_government_schemes.php");
        exit;
    }
} else {
    header("Location: admin_government_schemes.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Show Scheme</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .container {
            max-width: 600px;
            margin: 80px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        h1 {
            text-align: center;
            color: #2e7d32;
        }

        p {
            font-size: 16px;
            line-height: 1.5;
        }

        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }

        .back-btn:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($scheme['name']); ?></h1>
        <p><?php echo htmlspecialchars($scheme['description']); ?></p>
        <a href="admin_government_schemes.php" class="back-btn">Back to Schemes</a>
    </div>
</body>
</html> 