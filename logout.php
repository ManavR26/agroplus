<?php
// Initialize the session
session_start();

// Prevent caching of the logout response
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Unset all session variables
$_SESSION = array();

// Delete the session cookie if it exists
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to exact absolute index URL
header("Location: http://localhost/agroplus/index.php");
exit;
?>