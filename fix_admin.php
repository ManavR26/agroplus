<?php
require_once "includes/config.php";

// First recreate the users table with correct structure
$drop_table = "DROP TABLE IF EXISTS users";
mysqli_query($conn, $drop_table);

$create_table = "CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    user_type ENUM('farmer', 'customer', 'admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if(!mysqli_query($conn, $create_table)) {
    die("Error creating users table: " . mysqli_error($conn));
}

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
    echo "<div class='success'>";
    echo "Admin user created successfully!<br>";
    echo "ID: " . $admin_id . "<br>";
    echo "Email: admin@admin.com<br>";
    echo "Password: admin123<br><br>";
    
    // Verify admin exists
    $verify_sql = "SELECT id, email, user_type FROM users WHERE email = 'admin@admin.com'";
    $verify_result = mysqli_query($conn, $verify_sql);
    if($admin = mysqli_fetch_assoc($verify_result)) {
        echo "Verification successful:<br>";
        echo "Found admin with ID: " . $admin['id'] . "<br>";
        echo "User type: " . $admin['user_type'] . "<br><br>";
    }
    
    echo "<a href='admin_login.php' class='btn'>Login as Admin</a>";
    echo "</div>";
} else {
    echo "<div class='error'>";
    echo "Error creating admin: " . mysqli_error($conn);
    echo "</div>";
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
        padding: 20px;
        max-width: 600px;
        margin: 0 auto;
        background: #f5f5f5;
    }
    .success, .error {
        padding: 20px;
        border-radius: 8px;
        background: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    .success {
        border-left: 4px solid #4CAF50;
    }
    .error {
        border-left: 4px solid #f44336;
    }
    .btn {
        display: inline-block;
        background: #4CAF50;
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 4px;
    }
    .btn:hover {
        background: #45a049;
    }
</style> 