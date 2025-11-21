<?php
require_once "includes/config.php";
session_start();

// Strict admin check
if(!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "admin"){
    header("location: admin_login.php");
    exit;
}

// Fetch the scheme to edit
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitize ID input
    $query = "SELECT * FROM government_schemes WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    if($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $scheme = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        if(!$scheme) {
            header("Location: admin_government_schemes.php");
            exit;
        }
    } else {
        header("Location: admin_government_schemes.php");
        exit;
    }
} else {
    header("Location: admin_government_schemes.php");
    exit;
}

// Handle form submission for updating the scheme
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    
    // Input validation
    if(empty($name) || empty($description)) {
        $error = "All fields are required.";
    } else {
        $query = "UPDATE government_schemes SET name = ?, description = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        if($stmt) {
            mysqli_stmt_bind_param($stmt, "ssi", $name, $description, $id);
            if(mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header("Location: admin_government_schemes.php");
                exit;
            } else {
                $error = "Error updating scheme. Please try again.";
            }
            mysqli_stmt_close($stmt);
        } else {
            $error = "Error preparing update statement.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Scheme</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .container {
            max-width: 600px;
            margin: 80px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        h1 {
            text-align: center;
            color: #2e7d32;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Scheme</h1>
        
        <?php if(isset($error)): ?>
            <div style="color: red; margin-bottom: 15px; padding: 10px; background: #ffebee; border: 1px solid red; border-radius: 4px;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="text" name="name" value="<?php echo htmlspecialchars($scheme['name']); ?>" required>
            <textarea name="description" required><?php echo htmlspecialchars($scheme['description']); ?></textarea>
            <button type="submit">Update Scheme</button>
        </form>
    </div>
</body>
</html> 