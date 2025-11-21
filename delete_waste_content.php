<?php
require_once "includes/config.php";
session_start();

// Check admin access
if(!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "admin"){
    header("location: index.php");
    exit;
}

// Check if ID is set
if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    
    // Prepare the delete statement
    $sql = "DELETE FROM waste_management_content WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if(mysqli_stmt_execute($stmt)){
        header("location: admin_waste_management.php?success=Content deleted successfully.");
    } else {
        header("location: admin_waste_management.php?error=Error deleting content.");
    }
} else {
    header("location: admin_waste_management.php?error=Invalid request.");
}
?> 