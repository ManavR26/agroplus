<?php
require_once "includes/config.php";

// Create users table if not exists
$create_table = "CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    user_type VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

mysqli_query($conn, $create_table);

// Remove existing admin
mysqli_query($conn, "DELETE FROM users WHERE email = 'admin@admin.com'");

// Create new admin with simple password
$sql = "INSERT INTO users (username, email, password, user_type) VALUES ('admin', 'admin@admin.com', '" . password_hash('admin123', PASSWORD_DEFAULT) . "', 'admin')";

if(mysqli_query($conn, $sql)) {
    // Set session variables directly
    session_start();
    $_SESSION['user_id'] = mysqli_insert_id($conn);
    $_SESSION['user_type'] = 'admin';
    
    echo "<div class='message'>";
    echo "Admin created successfully!<br><br>";
    echo "Email: admin@admin.com<br>";
    echo "Password: admin123<br><br>";
    echo "<a href='admin_organic_methods.php' class='btn'>Go to Admin Dashboard</a>";
    echo "</div>";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        padding: 20px;
        max-width: 500px;
        margin: 0 auto;
        background: #f5f5f5;
    }
    .message {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        text-align: center;
        line-height: 1.6;
    }
    .btn {
        display: inline-block;
        background: #4CAF50;
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 4px;
        margin-top: 10px;
    }
    .btn:hover {
        background: #45a049;
    }
</style> 