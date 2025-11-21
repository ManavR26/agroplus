<?php
require_once "includes/config.php";
session_start();

// Strict admin check
if(!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "admin"){
    header("location: admin_login.php");
    exit;
}

// Delete the scheme
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    mysqli_query($conn, "DELETE FROM government_schemes WHERE id = $id");
}

// Redirect back to the government schemes page
header("Location: admin_government_schemes.php"); 