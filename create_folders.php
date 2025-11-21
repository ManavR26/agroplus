<?php
// Define the project root directory
$project_root = dirname(__FILE__);

// Define directories to create
$directories = [
    $project_root . '/uploads',
    $project_root . '/uploads/waste_management',
    $project_root . '/uploads/organic_methods'
];

// Function to create directory and set permissions
function createDirectory($path) {
    if (!file_exists($path)) {
        if (mkdir($path, 0777, true)) {
            chmod($path, 0777);
            echo "✅ Created directory: " . basename($path) . "<br>";
            return true;
        } else {
            echo "❌ Failed to create: " . basename($path) . "<br>";
            echo "Error: " . error_get_last()['message'] . "<br>";
            return false;
        }
    } else {
        echo "Directory exists: " . basename($path) . "<br>";
        chmod($path, 0777);
        return true;
    }
}

echo "<h2>Creating Upload Directories</h2>";

// Create each directory
foreach ($directories as $dir) {
    createDirectory($dir);
}

// Test write permissions
echo "<h2>Testing Write Permissions</h2>";
foreach ($directories as $dir) {
    $test_file = $dir . '/test.txt';
    if (file_put_contents($test_file, 'test')) {
        echo "✅ Can write to: " . basename($dir) . "<br>";
        unlink($test_file); // Clean up
    } else {
        echo "❌ Cannot write to: " . basename($dir) . "<br>";
    }
}

// Display current directory permissions
echo "<h2>Current Permissions</h2>";
foreach ($directories as $dir) {
    if (file_exists($dir)) {
        $perms = substr(sprintf('%o', fileperms($dir)), -4);
        echo basename($dir) . ": " . $perms . "<br>";
    }
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        padding: 20px;
        line-height: 1.6;
    }
    h2 {
        color: #2e7d32;
        margin-top: 20px;
    }
</style> 