<?php
require_once "includes/config.php";
session_start();

// Strict admin check
if(!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== "admin"){
    header("location: admin_login.php");
    exit;
}

// Handle form submission for new government scheme
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);

    // Input validation
    if(empty($name) || empty($description)) {
        $message = "Error: All fields are required.";
    } else {
        // Insert new scheme into the database using prepared statements
        $query = "INSERT INTO government_schemes (name, description) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        if($stmt) {
            mysqli_stmt_bind_param($stmt, "ss", $name, $description);
            if (mysqli_stmt_execute($stmt)) {
                $message = "New scheme created successfully.";
            } else {
                $message = "Error: Unable to create scheme. Please try again.";
            }
            mysqli_stmt_close($stmt);
        } else {
            $message = "Error: Unable to prepare statement.";
        }
    }
}

// Ensure table exists to avoid fatal errors on fresh setups
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS government_schemes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Fetch government schemes from the database
$schemes = mysqli_query($conn, "SELECT * FROM government_schemes");

// Check for query errors
if (!$schemes) {
    error_log("Database query failed: " . mysqli_error($conn));
    die("Unable to load schemes. Please try again later.");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Government Schemes - Admin Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 100px auto 40px; /* Increased top margin for navbar */
            padding: 30px; /* Increased padding */
            border: 1px solid #ddd;
            border-radius: 12px; /* Rounded corners */
            background-color: #f9f9f9;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1); /* Added shadow */
        }

        h1 {
            text-align: center;
            color: #2e7d32;
            margin-bottom: 30px; /* Added bottom margin */
            font-size: 2.2em;
            font-weight: bold;
        }

        .form-section {
            background: white;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 30px; /* Space between form and table */
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .form-title {
            color: #2e7d32;
            margin-bottom: 20px;
            font-size: 1.3em;
            font-weight: bold;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px; /* Increased gap between form elements */
        }

        input, textarea {
            padding: 12px 15px; /* Increased padding */
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            width: 100%;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: #4CAF50;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        button {
            padding: 12px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #45a049;
        }

        .table-section {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .table-title {
            color: #2e7d32;
            margin-bottom: 20px;
            font-size: 1.3em;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }

        th, td {
            padding: 15px 12px; /* Increased padding */
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        th {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .actions-cell {
            white-space: nowrap;
        }

        .action-btn {
            padding: 8px 12px; /* Increased padding */
            border-radius: 4px;
            text-decoration: none;
            color: white;
            font-size: 14px;
            margin-right: 8px; /* Increased margin between buttons */
            margin-bottom: 4px; /* Added bottom margin for wrapping */
            display: inline-block;
            transition: all 0.3s ease;
        }

        .edit-btn {
            background: #2196F3;
        }

        .edit-btn:hover {
            background: #1976D2;
        }

        .delete-btn {
            background: #f44336;
        }

        .delete-btn:hover {
            background: #d32f2f;
        }

        .show-btn {
            background: #4CAF50;
        }

        .show-btn:hover {
            background: #388E3C;
        }

        .message {
            margin: 20px 0;
            padding: 15px 20px;
            border-radius: 6px;
            background-color: #e7f3fe;
            color: #31708f;
            border-left: 4px solid #2196F3;
        }

        /* Back button styling */
        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        .back-btn:hover {
            background: #5a6268;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="admin-container">
        <a href="admin_dashboard.php" class="back-btn">← Back to Dashboard</a>
        
        <h1>Manage Government Schemes</h1>

        <?php if (isset($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- Form Section -->
        <div class="form-section">
            <h2 class="form-title">Add New Government Scheme</h2>
            <form method="POST" action="">
                <input type="text" name="name" placeholder="Scheme Name" required>
                <textarea name="description" placeholder="Scheme Description" required></textarea>
                <button type="submit">Add Scheme</button>
            </form>
        </div>

        <!-- Table Section -->
        <div class="table-section">
            <h2 class="table-title">Existing Government Schemes</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($scheme = mysqli_fetch_assoc($schemes)): ?>
                        <tr>
                            <td><?php echo $scheme['id']; ?></td>
                            <td><?php echo htmlspecialchars($scheme['name']); ?></td>
                            <td><?php echo htmlspecialchars($scheme['description']); ?></td>
                            <td class="actions-cell">
                                <a href="edit_scheme.php?id=<?php echo $scheme['id']; ?>" class="action-btn edit-btn">Edit</a>
                                <a href="delete_scheme.php?id=<?php echo $scheme['id']; ?>" class="action-btn delete-btn">Delete</a>
                                <a href="show_scheme.php?id=<?php echo $scheme['id']; ?>" class="action-btn show-btn">Show</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html> 