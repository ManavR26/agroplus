<?php
require_once "includes/config.php";
session_start();

// Check if user is logged in
if(!isset($_SESSION["user_id"])){
    header("location: login.php");
    exit;
}

// Fetch latest user data from database
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION["user_id"]);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Update session with latest data
$_SESSION["username"] = $user["username"];
$_SESSION["email"] = $user["email"];
$_SESSION["mobile"] = $user["mobile"];
$_SESSION["address"] = $user["address"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - AgroPlus</title>
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

        .form-group p {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            background: #f9f9f9;
            min-height: 38px;
            line-height: 22px;
        }

        .form-group textarea {
            height: 100px;
            resize: vertical;
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
        }

        .error-msg {
            color: #f44336;
            text-align: center;
            margin-bottom: 20px;
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
            <h2>Profile Details</h2>
        </div>

        <div class="form-group">
            <label>Username</label>
            <p><?php echo htmlspecialchars($user['username']); ?></p>
        </div>

        <div class="form-group">
            <label>Email</label>
            <p><?php echo htmlspecialchars($user['email']); ?></p>
        </div>

        <div class="form-group">
            <label>Mobile Number</label>
            <p><?php echo htmlspecialchars($user['mobile'] ?? 'Not set'); ?></p>
        </div>

        <div class="form-group">
            <label>Address</label>
            <p><?php echo htmlspecialchars($user['address'] ?? 'Not set'); ?></p>
        </div>

        <div class="form-group">
            <label>Account Type</label>
            <p><?php echo ucfirst(htmlspecialchars($user['user_type'])); ?></p>
        </div>

        <div class="btn-container">
            <a href="edit_profile.php" class="submit-btn">Edit Profile</a>
            <a href="change_password.php" class="cancel-btn">Change Password</a>
        </div>
    </div>
</body>
</html> 