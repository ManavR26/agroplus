<?php
require_once "includes/config.php";
session_start();

// Check if already logged in
if(isset($_SESSION["user_id"])){
    header("location: index.php");
    exit;
}

// Process registration form
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);
    $user_type = trim($_POST["user_type"]);
    
    // Validate input
    if($password !== $confirm_password){
        $error = "Passwords do not match.";
    } else {
        // Check if email exists
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if(mysqli_stmt_num_rows($stmt) > 0){
            $error = "This email is already registered.";
        } else {
            // Insert new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, email, password, user_type) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $hashed_password, $user_type);
            
            if(mysqli_stmt_execute($stmt)){
                header("location: login.php");
                exit;
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - AgroPlus</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background: url('assets/images/irewolede-PvwdlXqo85k-unsplash.jpg') center center / cover no-repeat fixed;
        }

        .register-container {
            max-width: 400px;
            margin: 80px auto 0;
            padding: 20px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .register-header h2 {
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

        .form-group input,
        .form-group select {
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

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .login-link a {
            color: #2e7d32;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="register-container">
        <div class="register-header">
            <h2>Create Account</h2>
            <p>Join AgroPlus today!</p>
        </div>

        <?php if(isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <div class="form-group">
                <label for="user_type">Register as</label>
                <select id="user_type" name="user_type" required>
                    <option value="farmer">Farmer</option>
                    <option value="customer">Customer</option>
                </select>
            </div>

            <button type="submit" class="submit-btn">Register</button>
        </form>

        <div class="login-link">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</body>
</html> 