<?php
require_once "includes/config.php";

echo "<h2>Database Connection Test</h2>";

// Test database connection
if ($conn) {
    echo "<p style='color: green;'>✓ Database connection successful!</p>";
} else {
    echo "<p style='color: red;'>✗ Database connection failed!</p>";
    die();
}

// Test if tables exist
$tables = ['users', 'products', 'orders', 'order_items', 'waste_management_content', 'organic_methods_content'];
echo "<h3>Checking Tables:</h3>";

foreach ($tables as $table) {
    $query = "SHOW TABLES LIKE '$table'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        echo "<p style='color: green;'>✓ Table '$table' exists</p>";
    } else {
        echo "<p style='color: red;'>✗ Table '$table' does not exist</p>";
    }
}

// Test admin account
$sql = "SELECT id, email, user_type FROM users WHERE email = 'admin@admin.com'";
$result = mysqli_query($conn, $sql);

if ($admin = mysqli_fetch_assoc($result)) {
    echo "<p style='color: green;'>✓ Admin account exists (ID: {$admin['id']})</p>";
} else {
    echo "<p style='color: red;'>✗ Admin account not found</p>";
}

// Test inserting a sample user
echo "<h3>Testing Sample Data Insertion:</h3>";
try {
    $username = "test_user_" . rand(1000, 9999);
    $password = password_hash("test123", PASSWORD_DEFAULT);
    $email = "test" . rand(1000, 9999) . "@example.com";
    $user_type = "customer";

    $sql = "INSERT INTO users (username, password, email, user_type) 
            VALUES (?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $username, $password, $email, $user_type);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<p style='color: green;'>✓ Sample user created successfully</p>";
        
        // Clean up - delete test user
        $user_id = mysqli_insert_id($conn);
        mysqli_query($conn, "DELETE FROM users WHERE id = $user_id");
        echo "<p style='color: blue;'>ℹ Test user removed</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to create sample user</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

// Display table structures
echo "<h3>Table Structures:</h3>";
foreach ($tables as $table) {
    $result = mysqli_query($conn, "DESCRIBE $table");
    if ($result) {
        echo "<h4>Table: $table</h4>";
        echo "<table border='1' style='margin-bottom: 20px;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>{$row['Field']}</td>";
            echo "<td>{$row['Type']}</td>";
            echo "<td>{$row['Null']}</td>";
            echo "<td>{$row['Key']}</td>";
            echo "<td>{$row['Default']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}

mysqli_close($conn);
?> 