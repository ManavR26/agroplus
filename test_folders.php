<?php
// Base upload directory
$base_dir = __DIR__ . '/assets/images/products';

// Create directory if it doesn't exist
if (!file_exists($base_dir)) {
    if (mkdir($base_dir, 0777, true)) {
        echo "✅ Created upload directory<br>";
        chmod($base_dir, 0777);
    } else {
        echo "❌ Failed to create directory<br>";
    }
} else {
    echo "Directory exists<br>";
    chmod($base_dir, 0777);
}

// Test write permissions
$test_file = $base_dir . '/test.txt';
if (file_put_contents($test_file, 'test')) {
    echo "✅ Write permissions OK<br>";
    unlink($test_file);
} else {
    echo "❌ Cannot write to directory<br>";
}
?> 