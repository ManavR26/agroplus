<?php
require_once "includes/config.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION["user_id"]) || $_SESSION["user_type"] != "farmer"){
    header("Location: login.php", true, 302);
    exit;
}

// Redirect farmers to single add-product panel
header("Location: upload_product.php", true, 301);
exit;
?>