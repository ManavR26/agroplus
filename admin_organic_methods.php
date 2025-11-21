<?php
require_once "includes/config.php";
session_start();

// Check admin access
if(!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "admin"){
    header("location: admin_login.php");
    exit;
}

// Verify admin ID exists in database
$verify_sql = "SELECT id FROM users WHERE id = ? AND user_type = 'admin'";
$verify_stmt = mysqli_prepare($conn, $verify_sql);
mysqli_stmt_bind_param($verify_stmt, "i", $_SESSION["user_id"]);
mysqli_stmt_execute($verify_stmt);
$verify_result = mysqli_stmt_get_result($verify_stmt);

if(mysqli_num_rows($verify_result) == 0) {
    // Admin ID not found, redirect to login
    session_destroy();
    header("location: admin_login.php");
    exit;
}

$admin_id = mysqli_fetch_assoc($verify_result)['id'];
mysqli_stmt_close($verify_stmt);

$success = $error = "";

// Handle form submission
if($_SERVER["REQUEST_METHOD"] == "POST"){
    try {
        $title = trim($_POST["title"]);
        $description = trim($_POST["description"]);
        $method_type = trim($_POST["method_type"]);
        
        // Handle image upload with security validation
        $target_dir = "uploads/organic_methods/";
        if(!file_exists($target_dir)){
            if(!mkdir($target_dir, 0777, true)) {
                throw new Exception("Failed to create upload directory");
            }
        }
        
        $image = "";
        $target_file = "";
        if(isset($_FILES["image"]) && $_FILES["image"]["error"] == 0){
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            $file_name = $_FILES["image"]["name"];
            $file_size = $_FILES["image"]["size"];
            $file_tmp = $_FILES["image"]["tmp_name"];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            // Validate file type
            if(!in_array($file_ext, $allowed_types)) {
                throw new Exception("Only JPG, JPEG, PNG, and GIF files are allowed.");
            }
            // Validate file size (max 5MB)
            if($file_size > 5242880) {
                throw new Exception("File size must be less than 5MB.");
            }
            // Validate if it's actually an image
            if(!getimagesize($file_tmp)) {
                throw new Exception("Invalid image file.");
            }
            
            $image = time() . '_' . uniqid() . '.' . $file_ext;
            $target_file = $target_dir . $image;
            if(!move_uploaded_file($file_tmp, $target_file)) {
                throw new Exception("Failed to upload image");
            }
        }
        
        // Insert into database using verified admin_id
        $sql = "INSERT INTO organic_methods_content (admin_id, title, description, method_type, image_name, image_path) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        
        if($stmt === false) {
            throw new Exception("Error preparing statement: " . mysqli_error($conn));
        }
        
        // Use verified admin_id
        if(!mysqli_stmt_bind_param($stmt, "isssss", $admin_id, $title, $description, $method_type, $image, $target_file)) {
            throw new Exception("Error binding parameters: " . mysqli_error($conn));
        }
        
        if(!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error executing statement: " . mysqli_error($conn));
        }
        
        mysqli_stmt_close($stmt);
        $success = "Content added successfully!";
        
    } catch (Exception $e) {
        $error = $e->getMessage();
        // Clean up uploaded file if there was an error
        if(isset($target_file) && file_exists($target_file)) {
            unlink($target_file);
        }
    }
}

// Fetch existing content
try {
    $sql = "SELECT * FROM organic_methods_content ORDER BY created_at DESC";
    $result = mysqli_query($conn, $sql);
    if($result === false) {
        throw new Exception("Error fetching content: " . mysqli_error($conn));
    }
} catch (Exception $e) {
    $error = $e->getMessage();
    $result = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Organic Methods - Admin</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }

        .content-form {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
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
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .form-group textarea {
            height: 120px;
            resize: vertical;
        }

        .btn-submit {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-submit:hover {
            background: #45a049;
        }

        .content-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .content-card {
            background: #fff;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .content-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .content-card h3 {
            margin: 10px 0;
            color: #333;
        }

        .content-card p {
            color: #666;
            margin-bottom: 10px;
        }

        .method-type {
            display: inline-block;
            padding: 3px 8px;
            background: #e8f5e9;
            color: #2e7d32;
            border-radius: 3px;
            font-size: 0.9em;
            margin-bottom: 10px;
        }

        .success-msg {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .error-msg {
            background: #ffebee;
            color: #c62828;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .delete-btn {
            background: #f44336;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            float: right;
        }

        .delete-btn:hover {
            background: #d32f2f;
        }

        @media (max-width: 768px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h1>Manage Organic Methods</h1>

        <div class="content-form">
            <?php if($success): ?>
                <div class="success-msg"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if($error): ?>
                <div class="error-msg"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" required>
                </div>

                <div class="form-group">
                    <label>Category</label>
                    <select name="method_type" required>
                        <option value="">Select Category</option>
                        <option value="pest_control">Natural Pest Control</option>
                        <option value="soil_management">Soil Management</option>
                        <option value="water_conservation">Water Conservation</option>
                        <option value="composting">Composting</option>
                        <option value="crop_rotation">Crop Rotation</option>
                        <option value="organic_fertilizers">Organic Fertilizers</option>
                        <option value="weed_management">Weed Management</option>
                        <option value="disease_control">Disease Control</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" required></textarea>
                </div>

                <div class="form-group">
                    <label>Image</label>
                    <input type="file" name="image" accept="image/*" required>
                </div>

                <button type="submit" class="btn-submit">Add Content</button>
            </form>
        </div>

        <h2>Existing Content</h2>
        <div class="content-grid">
            <?php if($result && mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <div class="content-card">
                        <div class="method-type">
                            <?php echo str_replace('_', ' ', ucwords($row['method_type'])); ?>
                        </div>
                        <?php if($row['image_path']): ?>
                            <img src="<?php echo htmlspecialchars($row['image_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($row['title']); ?>">
                        <?php endif; ?>
                        <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p><?php echo htmlspecialchars($row['description']); ?></p>
                        <small>Added: <?php echo date('M d, Y', strtotime($row['created_at'])); ?></small>
                        <button class="delete-btn" onclick="deleteContent(<?php echo $row['id']; ?>)">Delete</button>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No content available.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function deleteContent(id) {
            if(confirm('Are you sure you want to delete this content?')) {
                window.location.href = 'delete_organic_content.php?id=' + id;
            }
        }
    </script>
</body>
</html> 