<?php
require_once "includes/config.php";

// Test credentials
$test_email = "admin@agroplus.com";
$test_password = "admin123";

// Check if admin exists
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $test_email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if($user = mysqli_fetch_assoc($result)) {
    echo "Admin found in database:<br>";
    echo "Email: " . $user['email'] . "<br>";
    echo "User Type: " . $user['user_type'] . "<br>";
    
    // Test password
    if(password_verify($test_password, $user['password'])) {
        echo "<br>✅ Password verification successful!<br>";
        echo "You can now login with:<br>";
        echo "Email: admin@agroplus.com<br>";
        echo "Password: admin123";
    } else {
        echo "<br>❌ Password verification failed!";
    }
} else {
    echo "❌ Admin user not found in database";
}
?> 