<?php
require_once "includes/config.php";

// Drop existing admin
$delete_sql = "DELETE FROM users WHERE email = 'admin@admin.com'";
mysqli_query($conn, $delete_sql);

// Create new admin user
$username = "admin";
$email = "admin@admin.com";
$password = password_hash("admin123", PASSWORD_DEFAULT);
$user_type = "admin";

$sql = "INSERT INTO users (username, email, password, user_type) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $password, $user_type);

if(mysqli_stmt_execute($stmt)) {
    $admin_id = mysqli_insert_id($conn);
    echo "Admin user reset successfully!<br>";
    echo "ID: " . $admin_id . "<br>";
    echo "Email: admin@admin.com<br>";
    echo "Password: admin123<br>";
    echo "Please <a href='admin_login.php'>login here</a>";
} else {
    echo "Error resetting admin: " . mysqli_error($conn);
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
        padding: 20px;
        max-width: 600px;
        margin: 0 auto;
    }
    a {
        color: #4CAF50;
        text-decoration: none;
    }
    a:hover {
        text-decoration: underline;
    }
</style> 