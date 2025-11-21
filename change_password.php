<?php
require_once "includes/config.php";
session_start();

if(!isset($_SESSION["user_id"])){
    header("location: login.php");
    exit;
}

$success_msg = $error_msg = "";

// Handle password change
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $current_password = trim($_POST["current_password"]);
    $new_password = trim($_POST["new_password"]);
    $confirm_password = trim($_POST["confirm_password"]);
    
    // Verify current password
    $sql = "SELECT password FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $_SESSION["user_id"]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    
    if(password_verify($current_password, $user["password"])){
        if($new_password == $confirm_password){
            if(strlen($new_password) >= 6){
                // Update password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_sql = "UPDATE users SET password = ? WHERE id = ?";
                $stmt = mysqli_prepare($conn, $update_sql);
                mysqli_stmt_bind_param($stmt, "si", $hashed_password, $_SESSION["user_id"]);
                
                if(mysqli_stmt_execute($stmt)){
                    $success_msg = "Password updated successfully!";
                } else {
                    $error_msg = "Error updating password. Please try again.";
                }
            } else {
                $error_msg = "New password must be at least 6 characters long.";
            }
        } else {
            $error_msg = "New passwords do not match.";
        }
    } else {
        $error_msg = "Current password is incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - AgroPlus</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .edit-container {
            width: 90%;
            max-width: 600px;
            margin: 80px auto 20px;
            padding: 30px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .edit-header {
            text-align: center;
            margin-bottom: 30px;
            color: #4CAF50;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .btn-container {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }

        .submit-btn, 
        .cancel-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
        }

        .submit-btn {
            background: #4CAF50;
            color: white;
        }

        .cancel-btn {
            background: #f44336;
            color: white;
        }

        .success-msg {
            color: #4CAF50;
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background: #E8F5E9;
            border-radius: 4px;
        }

        .error-msg {
            color: #f44336;
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background: #FFEBEE;
            border-radius: 4px;
        }

        @media (max-width: 768px) {
            .edit-container {
                width: 95%;
                margin: 60px auto 20px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="edit-container">
        <div class="edit-header">
            <h2>Change Password</h2>
        </div>

        <?php if($success_msg): ?>
            <div class="success-msg"><?php echo $success_msg; ?></div>
        <?php endif; ?>

        <?php if($error_msg): ?>
            <div class="error-msg"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Current Password</label>
                <input type="password" name="current_password" required>
            </div>

            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" required>
            </div>

            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" required>
            </div>

            <div class="btn-container">
                <button type="submit" class="submit-btn">Update Password</button>
                <a href="profile.php" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html> 