<?php
require_once "includes/config.php";
session_start();

// Check if user is logged in
if(!isset($_SESSION["user_id"])){
    header("location: login.php");
    exit;
}

$success_msg = $error_msg = "";

// Fetch current user data
$sql = "SELECT username, email FROM users WHERE id = ?";  // Remove mobile and address from initial query
$stmt = mysqli_prepare($conn, $sql);

// Check if statement preparation was successful
if($stmt === false) {
    die("Error preparing statement: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $_SESSION["user_id"]);

if(!mysqli_stmt_execute($stmt)) {
    die("Error executing statement: " . mysqli_error($conn));
}

$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Initialize empty values for mobile and address
$user['mobile'] = '';
$user['address'] = '';

// Try to get mobile and address if columns exist
try {
    $extra_sql = "SELECT mobile, address FROM users WHERE id = ?";
    $extra_stmt = mysqli_prepare($conn, $extra_sql);
    if($extra_stmt) {
        mysqli_stmt_bind_param($extra_stmt, "i", $_SESSION["user_id"]);
        mysqli_stmt_execute($extra_stmt);
        $extra_result = mysqli_stmt_get_result($extra_stmt);
        $extra_data = mysqli_fetch_assoc($extra_result);
        if($extra_data) {
            $user['mobile'] = $extra_data['mobile'];
            $user['address'] = $extra_data['address'];
        }
        mysqli_stmt_close($extra_stmt);
    }
} catch(Exception $e) {
    // Silently handle the error - columns might not exist yet
}

// Handle form submission
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $mobile = trim($_POST["mobile"]);
    $address = trim($_POST["address"]);
    
    // Update user information
    $sql = "UPDATE users SET username = ?, email = ?, mobile = ?, address = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssi", $username, $email, $mobile, $address, $_SESSION["user_id"]);
    
    if(mysqli_stmt_execute($stmt)){
        // Update session data
        $_SESSION["username"] = $username;
        $_SESSION["email"] = $email;
        $_SESSION["mobile"] = $mobile;
        $_SESSION["address"] = $address;
        
        header("location: profile.php");
        exit;
    } else {
        $error = "Error updating profile. Please try again.";
    }
}

// Close the initial statement
if($stmt) {
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - AgroPlus</title>
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
        }

        .form-group input, 
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
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
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="edit-container">
        <div class="edit-header">
            <h2>Edit Profile</h2>
        </div>

        <?php if($success_msg): ?>
            <div class="success-msg"><?php echo $success_msg; ?></div>
        <?php endif; ?>

        <?php if($error_msg): ?>
            <div class="error-msg"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <div class="form-group">
                <label>Mobile Number</label>
                <input type="tel" name="mobile" value="<?php echo htmlspecialchars($user['mobile'] ?? ''); ?>" 
                       pattern="[0-9]{10}" title="Please enter a valid 10-digit mobile number">
            </div>

            <div class="form-group">
                <label>Address</label>
                <textarea name="address"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
            </div>

            <div class="btn-container">
                <button type="submit" class="submit-btn">Save Changes</button>
                <a href="profile.php" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html> 