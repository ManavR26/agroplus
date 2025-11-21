<?php
require_once "includes/config.php";
session_start();

// Check if user is logged in and is an admin
if(!isset($_SESSION["user_id"]) || $_SESSION["user_type"] != "admin"){
    header("location: admin_login.php");
    exit;
}

$success_msg = $error_msg = "";

// Handle form submission
if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['add_member'])) {
        $name = trim($_POST['name']);
        $position = trim($_POST['position']);
        $is_leader = isset($_POST['is_leader']) ? 1 : 0;
        
        // Handle photo upload
        $photo = "";
        if(isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['photo']['name'];
            $filetype = pathinfo($filename, PATHINFO_EXTENSION);
            
            if(in_array(strtolower($filetype), $allowed)) {
                $new_filename = uniqid() . '.' . $filetype;
                $upload_path = 'uploads/team/' . $new_filename;
                
                if(move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                    $photo = $new_filename;
                }
            }
        }
        
        $sql = "INSERT INTO team_members (name, position, photo, is_leader) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssi", $name, $position, $photo, $is_leader);
        
        if(mysqli_stmt_execute($stmt)) {
            $success_msg = "Team member added successfully!";
        } else {
            $error_msg = "Error adding team member.";
        }
    }
    
    // Handle member deletion
    if(isset($_POST['delete_member'])) {
        $member_id = $_POST['member_id'];
        
        // Get photo filename before deleting
        $sql = "SELECT photo FROM team_members WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $member_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $member = mysqli_fetch_assoc($result);
        
        // Delete photo file if exists
        if($member['photo']) {
            $photo_path = 'uploads/team/' . $member['photo'];
            if(file_exists($photo_path)) {
                unlink($photo_path);
            }
        }
        
        // Delete from database
        $sql = "DELETE FROM team_members WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $member_id);
        
        if(mysqli_stmt_execute($stmt)) {
            $success_msg = "Team member deleted successfully!";
        } else {
            $error_msg = "Error deleting team member.";
        }
    }
}

// Fetch all team members
$sql = "SELECT * FROM team_members ORDER BY is_leader DESC, name ASC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Team - Admin</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .admin-container {
            max-width: 1000px;
            margin: 80px auto 20px;
            padding: 20px;
        }
        
        .form-container {
            background: #fff;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .member-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .member-card {
            background: #f9f9f9;
            padding: 15px;
        }
        
        .member-photo {
            width: 100%;
            height: 200px;
            object-fit: cover;
            margin-bottom: 10px;
        }
        
        .success-msg {
            color: green;
            margin-bottom: 10px;
        }
        
        .error-msg {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="admin-container">
        <h1>Manage Team Members</h1>
        
        <?php if($success_msg): ?>
            <div class="success-msg"><?php echo $success_msg; ?></div>
        <?php endif; ?>
        
        <?php if($error_msg): ?>
            <div class="error-msg"><?php echo $error_msg; ?></div>
        <?php endif; ?>
        
        <div class="form-container">
            <h2>Add New Team Member</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="name" required>
                </div>
                
                <div class="form-group">
                    <label>Position:</label>
                    <input type="text" name="position" required>
                </div>
                
                <div class="form-group">
                    <label>Photo:</label>
                    <input type="file" name="photo" accept="image/*">
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_leader">
                        Is Leader
                    </label>
                </div>
                
                <button type="submit" name="add_member" class="submit-btn">Add Member</button>
            </form>
        </div>
        
        <div class="member-grid">
            <?php while($member = mysqli_fetch_assoc($result)): ?>
                <div class="member-card">
                    <?php if($member['photo']): ?>
                        <img src="uploads/team/<?php echo htmlspecialchars($member['photo']); ?>" 
                             alt="<?php echo htmlspecialchars($member['name']); ?>" 
                             class="member-photo">
                    <?php endif; ?>
                    
                    <h3><?php echo htmlspecialchars($member['name']); ?></h3>
                    <p><?php echo htmlspecialchars($member['position']); ?></p>
                    <?php if($member['is_leader']): ?>
                        <p><strong>Leader</strong></p>
                    <?php endif; ?>
                    
                    <form method="POST" style="margin-top: 10px;">
                        <input type="hidden" name="member_id" value="<?php echo $member['id']; ?>">
                        <button type="submit" name="delete_member" class="delete-btn" 
                                onclick="return confirm('Are you sure you want to delete this member?')">
                            Delete
                        </button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html> 