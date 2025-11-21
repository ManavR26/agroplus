<?php
require_once "includes/config.php";
require_once "includes/session_config.php";

// Check if already logged in
if(isset($_SESSION["user_id"])){
    if ($_SESSION["user_type"] === "farmer") {
        header("location: categories.php");
    } else if ($_SESSION["user_type"] === "customer") {
        header("location: customer_dashboard.php");
    } else {
        header("location: index.php");
    }
    exit;
}

// Process login form
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    
    $sql = "SELECT id, username, password, user_type FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if(mysqli_num_rows($result) == 1){
        $row = mysqli_fetch_assoc($result);
        if(password_verify($password, $row["password"])){
            // Regenerate session ID for security
            session_regenerate_id(true);
            
            $_SESSION["user_id"] = $row["id"];
            $_SESSION["username"] = $row["username"];
            $_SESSION["user_type"] = $row["user_type"];
            $_SESSION["last_activity"] = time();
            
            if($row["user_type"] == "farmer") {
                header("Location: categories.php", true, 303);
            } else if($row["user_type"] == "customer") {
                header("Location: customer_dashboard.php", true, 303);
            } else {
                header("Location: index.php", true, 303);
            }
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AgroPlus</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background: url('assets/images/irewolede-PvwdlXqo85k-unsplash.jpg') center center / cover no-repeat fixed;
        }

        .login-container {
            max-width: 400px;
            margin: 80px auto 0;
            padding: 20px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h2 {
            color: #2e7d32;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .submit-btn {
            width: 100%;
            padding: 12px;
            background: #2e7d32;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }

        .submit-btn:hover {
            background: #1b5e20;
        }

        .error-message {
            color: #c62828;
            text-align: center;
            margin-bottom: 20px;
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
        }

        .register-link a {
            color: #2e7d32;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="login-container">
        <div class="login-header">
            <h2>Login to AgroPlus</h2>
            <p>Welcome back! Please login to your account.</p>
        </div>

        <?php if(isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="submit-btn">Login</button>
        </form>

        <div class="register-link">
            <p>Don't have an account? <a href="register.php">Sign up here</a></p>
        </div>
    </div>
</body>
</html> 