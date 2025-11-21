<?php
require_once "includes/config.php";
session_start();

// If already logged in as admin, redirect to admin dashboard
if(isset($_SESSION["user_type"]) && $_SESSION["user_type"] === "admin"){
    header("location: admin_dashboard.php");
    exit;
}

$error = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    
    // Default admin credentials
    if($email === "admin@admin.com" && $password === "admin123") {
        // Set session variables
        $_SESSION["user_id"] = 1; // Default admin ID
        $_SESSION["username"] = "admin";
        $_SESSION["user_type"] = "admin";
        
        header("Location: admin_dashboard.php", true, 303); // PRG: avoid form resubmission on back/refresh
        exit;
    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - AgroPlus</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <style>
        /* Hide specific navigation items on admin login page */
        .nav-items a[href="index.php"],
        .nav-items a[href="login.php"],
        .nav-items a[href="register.php"] {
            display: none;
        }
        
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        body {
            background: url('assets/images/irewolede-PvwdlXqo85k-unsplash.jpg') center center / cover no-repeat fixed;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .error-msg {
            color: #f44336;
            margin-bottom: 20px;
            text-align: center;
        }

        .credentials-hint {
            margin-top: 20px;
            text-align: center;
            color: #666;
            font-size: 0.9em;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    

    <div class="login-container">
        <h2 style="text-align: center; margin-bottom: 30px;">Admin Login</h2>
        
        <?php if($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit" class="login-btn">Login</button>
        </form>

        <!-- Added credentials hint -->
        <div class="credentials-hint">
            <strong>Default Credentials:</strong><br>
            Email: admin@admin.com<br>
            Password: admin123
        </div>
    </div>
</body>
</html> 