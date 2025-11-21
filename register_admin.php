<?php
require_once "includes/config.php";

// Admin credentials
$username = "admin";
$email = "admin@agroplus.com";
$password = "admin123";
$user_type = "admin";

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// First, delete any existing admin
$sql = "DELETE FROM users WHERE email = ? OR username = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ss", $email, $username);
mysqli_stmt_execute($stmt);

// Insert new admin
$sql = "INSERT INTO users (username, email, password, user_type) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $hashed_password, $user_type);

if(mysqli_stmt_execute($stmt)){
    echo "Admin user created successfully!<br>";
    echo "Email: admin@agroplus.com<br>";
    echo "Password: admin123<br>";
    echo "You can now login with these credentials.";
} else {
    echo "Error creating admin user: " . mysqli_error($conn);
}
?> 