<?php
// Database configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'agroplus_db');

// Attempt to connect to MySQL database
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($conn === false){
    // Log the actual error for debugging (in production, log to file)
    error_log("Database connection failed: " . mysqli_connect_error());
    // Display generic error to user
    die("Database connection error. Please try again later.");
}
?> 