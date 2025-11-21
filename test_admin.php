<?php
require_once "includes/config.php";

// Test credentials
$email = "admin@agroplus.com";
$password = "admin123";

// Query to check admin
$sql = "SELECT id, username, password, user_type FROM users WHERE email = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) == 1){
    $row = mysqli_fetch_assoc($result);
    if(password_verify($password, $row["password"])){
        echo "Admin credentials are valid!";
        echo "<br>Username: " . $row["username"];
        echo "<br>User Type: " . $row["user_type"];
    } else {
        echo "Password verification failed!";
    }
} else {
    echo "Admin email not found!";
}
?> 