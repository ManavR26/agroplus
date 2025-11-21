<?php
require_once "includes/config.php";

// Check admin in database
$sql = "SELECT id, email, user_type, password FROM users WHERE user_type = 'admin'";
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) > 0) {
    $admin = mysqli_fetch_assoc($result);
    echo "Admin exists:<br>";
    echo "ID: " . $admin['id'] . "<br>";
    echo "Email: " . $admin['email'] . "<br>";
    echo "User Type: " . $admin['user_type'] . "<br>";
} else {
    echo "No admin found in database.<br>";
    echo "Please visit <a href='setup_admin.php'>setup_admin.php</a> to create admin account.";
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