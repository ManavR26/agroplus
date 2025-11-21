<?php
// Test file paths
$waste_dir = "uploads/waste_management";
$organic_dir = "uploads/organic_methods";

// Function to test directory
function test_directory($dir) {
    echo "<h3>Testing directory: $dir</h3>";
    
    // Check if directory exists
    if (!file_exists($dir)) {
        echo "❌ Directory does not exist<br>";
        // Try to create it
        if (mkdir($dir, 0777, true)) {
            echo "✅ Directory created successfully<br>";
        } else {
            echo "❌ Failed to create directory<br>";
        }
    } else {
        echo "✅ Directory exists<br>";
    }
    
    // Test write permissions
    $test_file = $dir . "/test.txt";
    if (file_put_contents($test_file, "Test write")) {
        echo "✅ Write permission OK<br>";
        unlink($test_file); // Clean up test file
    } else {
        echo "❌ Cannot write to directory<br>";
    }
    
    // Check directory permissions
    $perms = substr(sprintf('%o', fileperms($dir)), -4);
    echo "Current permissions: $perms<br>";
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Upload Directory Test</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        h3 { color: #2e7d32; }
    </style>
</head>
<body>
    <h2>Testing Upload Directories</h2>
    <?php
    test_directory($waste_dir);
    echo "<br>";
    test_directory($organic_dir);
    ?>
</body>
</html> 