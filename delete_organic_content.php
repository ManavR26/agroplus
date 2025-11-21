<?php
require_once "includes/config.php";
session_start();

// Check admin access
if(!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "admin"){
    header("location: admin_login.php");
    exit;
}

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // First get the image path
    $sql = "SELECT image_path FROM organic_methods_content WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    // Delete the record
    $sql = "DELETE FROM organic_methods_content WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if(mysqli_stmt_execute($stmt)) {
        // Delete the image file if it exists
        if($row && $row['image_path'] && file_exists($row['image_path'])) {
            unlink($row['image_path']);
        }
        header("location: admin_organic_methods.php?msg=deleted");
    } else {
        header("location: admin_organic_methods.php?error=delete_failed");
    }
} else {
    header("location: admin_organic_methods.php");
}
?> 