<?php
require_once "includes/config.php";
session_start();

// Check if user is logged in and is a farmer
if(!isset($_SESSION["user_id"]) || $_SESSION["user_type"] != "farmer"){
    header("location: login.php");
    exit;
}

$success_message = $error_message = "";

// Handle product upload
if($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $name = trim($_POST["name"]);
        $description = trim($_POST["description"]);
        $price = floatval($_POST["price"]);
        $quantity = intval($_POST["quantity"]);
        
        // Create upload directory if it doesn't exist
        $target_dir = "assets/images/products/";
        if (!file_exists($target_dir)) {
            if (!mkdir($target_dir, 0777, true)) {
                throw new Exception("Failed to create upload directory");
            }
        }
        
        // Handle image upload
        $image_path = "";
        if(isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
            $allowed = ["jpg" => "image/jpg", "jpeg" => "image/jpeg", "png" => "image/png"];
            $filename = $_FILES["image"]["name"];
            $filetype = $_FILES["image"]["type"];
            $filesize = $_FILES["image"]["size"];
            
            // Validate file type
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if(!array_key_exists($ext, $allowed)) {
                throw new Exception("Please select a valid image format (JPG, JPEG, PNG)");
            }
            
            // Validate file size (5MB max)
            if($filesize > 5242880) {
                throw new Exception("File is too large. Maximum size is 5MB");
            }
            
            // Generate unique filename
            $new_filename = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "", $filename);
            $image_path = $target_dir . $new_filename;
            
            // Move uploaded file
            if(!move_uploaded_file($_FILES["image"]["tmp_name"], $image_path)) {
                throw new Exception("Failed to upload image");
            }
        }
        
        // Insert into database
        $sql = "INSERT INTO products (farmer_id, name, description, price, stock, image) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        
        if($stmt === false) {
            throw new Exception("Error preparing statement: " . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($stmt, "issdis", 
            $_SESSION["user_id"],
            $name,
            $description,
            $price,
            $quantity,
            $image_path
        );
        
        if(!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error executing statement: " . mysqli_error($conn));
        }
        
        mysqli_stmt_close($stmt);
        header("Location: farmer_dashboard.php?success=1");
        exit;
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
        // Clean up uploaded file if there was an error
        if(isset($image_path) && file_exists($image_path)) {
            unlink($image_path);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Product - AgroPlus</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .upload-container {
            max-width: 800px;
            margin: 80px auto 0;
            padding: 30px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .upload-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .upload-header h2 {
            color: #2e7d32;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .upload-form {
            display: grid;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            font-weight: bold;
            color: #333;
        }

        .form-group input,
        .form-group textarea {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-group textarea {
            height: 120px;
            resize: vertical;
        }

        .image-preview {
            width: 200px;
            height: 200px;
            border: 2px dashed #ddd;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 10px;
            overflow: hidden;
        }

        .image-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }

        .submit-btn {
            background: #2e7d32;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            background: #1b5e20;
            transform: translateY(-2px);
        }

        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }

        .success {
            background: #c8e6c9;
            color: #2e7d32;
        }

        .error {
            background: #ffcdd2;
            color: #c62828;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="upload-container">
        <div class="upload-header">
            <h2>Upload New Product</h2>
            <p>Add your agricultural product to the marketplace</p>
        </div>

        <?php if($success_message): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if($error_message): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form class="upload-form" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required></textarea>
            </div>

            <div class="form-group">
                <label for="price">Price (₹)</label>
                <input type="number" id="price" name="price" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="quantity">Quantity Available</label>
                <input type="number" id="quantity" name="quantity" required>
            </div>

            <div class="form-group">
                <label for="image">Product Image</label>
                <input type="file" id="image" name="image" accept="image/*" required>
                <div class="image-preview" id="imagePreview">
                    <span>Image Preview</span>
                </div>
            </div>

            <button type="submit" class="submit-btn">Upload Product</button>
        </form>
    </div>

    <script>
        // Image preview
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('imagePreview');

        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if(file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html> 