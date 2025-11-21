<?php
require_once "includes/config.php";
session_start();

// Check admin access
if(!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "admin"){
    header("location: admin_login.php");
    exit;
}

$success = $error = "";

// Handle form submission
if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(isset($_POST["delete_item"])) {
        // Handle deletion
        $item_id = $_POST["item_id"];
        $delete_sql = "DELETE FROM waste_management_content WHERE id = ?";
        $stmt = mysqli_prepare($conn, $delete_sql);
        mysqli_stmt_bind_param($stmt, "i", $item_id);
        
        if(mysqli_stmt_execute($stmt)){
            $success = "Content deleted successfully!";
        } else {
            $error = "Error deleting content.";
        }
    } else {
        // Handle adding new content
        $title = trim($_POST["title"]);
        $description = trim($_POST["description"]);
        
        // Handle image upload with security validation
        $target_dir = "uploads/waste_management/";
        if(!file_exists($target_dir)){
            mkdir($target_dir, 0777, true);
        }
        
        $image = "";
        if(isset($_FILES["image"]) && $_FILES["image"]["error"] == 0){
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            $file_name = $_FILES["image"]["name"];
            $file_size = $_FILES["image"]["size"];
            $file_tmp = $_FILES["image"]["tmp_name"];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            // Validate file type
            if(!in_array($file_ext, $allowed_types)) {
                $error = "Only JPG, JPEG, PNG, and GIF files are allowed.";
            }
            // Validate file size (max 5MB)
            elseif($file_size > 5242880) {
                $error = "File size must be less than 5MB.";
            }
            // Validate if it's actually an image
            elseif(!getimagesize($file_tmp)) {
                $error = "Invalid image file.";
            }
            else {
                $image = time() . '_' . uniqid() . '.' . $file_ext;
                $target_file = $target_dir . $image;
                if(!move_uploaded_file($file_tmp, $target_file)) {
                    $error = "Failed to upload image.";
                }
            }
        }
        
        if(empty($error)) {
            $sql = "INSERT INTO waste_management_content (title, description, image) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            if($stmt) {
                mysqli_stmt_bind_param($stmt, "sss", $title, $description, $image);
                
                if(mysqli_stmt_execute($stmt)){
                    $success = "Content added successfully!";
                } else {
                    $error = "Error adding content.";
                }
                mysqli_stmt_close($stmt);
            } else {
                $error = "Error preparing statement.";
            }
        }
    }
}

// Fetch existing content
$sql = "SELECT * FROM waste_management_content ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

// Check if the query was successful
if (!$result) {
    die("ERROR: Could not execute $sql. " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Waste Management - Admin</title>
    <style>
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .back-btn {
            padding: 8px 16px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .form-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input[type="text"],
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-group textarea {
            height: 150px;
        }
        .submit-btn, .delete-btn {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }
        .delete-btn {
            background: #dc3545;
        }
        .content-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .content-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .content-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 4px;
            margin: 10px 0 8px 0;
            display: block;
        }
        .content-item small {
            display: block;
            color: #666;
            margin-top: 4px;
        }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Manage Waste Management Content</h1>
            <a href="admin_dashboard.php" class="back-btn">Back to Dashboard</a>
        </div>

        <div class="form-container">
            <?php if($success): ?>
                <div class="success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" required>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Image</label>
                    <input type="file" name="image" accept="image/*" required>
                </div>
                
                <button type="submit" class="submit-btn">Add Content</button>
            </form>
        </div>

        <h2>Existing Content</h2>
        <div class="content-list">
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <div class="content-item">
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p><?php echo htmlspecialchars($row['description']); ?></p>
                    <?php if($row['image']): ?>
                        <img src="uploads/waste_management/<?php echo htmlspecialchars($row['image']); ?>" alt="Content Image">
                    <?php endif; ?>
                    <small>Added: <?php echo date('M d, Y', strtotime($row['created_at'])); ?></small>
                    <form method="POST">
                        <input type="hidden" name="item_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="delete_item" class="delete-btn" 
                                onclick="return confirm('Are you sure you want to delete this item?');">
                            Delete
                        </button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html> 