<?php
require_once "includes/config.php";

// First check if admin exists
$check_sql = "SELECT id FROM users WHERE email = 'admin@admin.com' AND user_type = 'admin'";
$result = mysqli_query($conn, $check_sql);

if(mysqli_num_rows($result) == 0) {
    // Create admin user if none exists
    $username = "admin";
    $email = "admin@admin.com";
    $password = password_hash("admin123", PASSWORD_DEFAULT);
    $user_type = "admin";
    
    // First delete any existing admin
    mysqli_query($conn, "DELETE FROM users WHERE email = 'admin@admin.com'");
    
    $sql = "INSERT INTO users (username, email, password, user_type) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $password, $user_type);
    
    if(mysqli_stmt_execute($stmt)) {
        echo "Admin user created successfully!<br>";
        echo "Email: admin@admin.com<br>";
        echo "Password: admin123<br>";
        echo "<a href='admin_login.php'>Go to Login</a>";
    } else {
        echo "Error creating admin: " . mysqli_error($conn);
    }
} else {
    echo "Admin already exists. <a href='admin_login.php'>Go to Login</a>";
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