<?php
require_once "includes/config.php";

// Create test admin credentials
$email = "admin@agroplus.com";
$password = "admin123";

// Test the login
$sql = "SELECT id, username, password, user_type FROM users WHERE email = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if($row = mysqli_fetch_assoc($result)) {
    echo "<h2>Admin User Found:</h2>";
    echo "Username: " . $row['username'] . "<br>";
    echo "User Type: " . $row['user_type'] . "<br>";
    
    if(password_verify($password, $row['password'])) {
        echo "<p style='color: green;'>Password verification successful!</p>";
        echo "<p>You can now login with:<br>";
        echo "Email: admin@agroplus.com<br>";
        echo "Password: admin123</p>";
    } else {
        echo "<p style='color: red;'>Password verification failed!</p>";
        
        // Create new hash for debugging
        $new_hash = password_hash($password, PASSWORD_DEFAULT);
        echo "<p>Debug Info:<br>";
        echo "Stored Hash: " . $row['password'] . "<br>";
        echo "New Hash: " . $new_hash . "</p>";
    }
} else {
    echo "<p style='color: red;'>Admin user not found in database!</p>";
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
        line-height: 1.6;
    }
    h2 {
        color: #2e7d32;
    }
</style> 