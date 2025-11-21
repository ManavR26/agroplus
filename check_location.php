<?php
echo "<h2>File Location Check</h2>";
echo "<pre>";
echo "Current File: " . __FILE__ . "\n";
echo "Project Directory: " . dirname(__FILE__) . "\n";
echo "Files in current directory:\n";
print_r(scandir(dirname(__FILE__)));
echo "</pre>";
?> 