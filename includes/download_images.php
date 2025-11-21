<?php
// Simple utility to download themed images to assets/images/
// Usage (CLI or browser): download_images.php?name=browse-products&query=farm,produce,market

function ensure_dir($dir){
    if(!is_dir($dir)){
        mkdir($dir, 0777, true);
    }
}

$name = isset($_GET['name']) ? preg_replace('/[^a-z0-9\-]/i', '-', $_GET['name']) : '';
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
if($name === '' || $query === ''){
    http_response_code(400);
    echo 'Missing name or query';
    exit;
}

$targetDir = __DIR__ . '/../assets/images/auto/';
ensure_dir($targetDir);

$url = 'https://source.unsplash.com/1200x800/?' . urlencode($query);
$imageData = @file_get_contents($url);
if($imageData === false){
    http_response_code(502);
    echo 'Failed to download image';
    exit;
}

$file = $targetDir . $name . '.jpg';
file_put_contents($file, $imageData);

header('Content-Type: application/json');
echo json_encode(['saved' => true, 'path' => 'assets/images/auto/' . $name . '.jpg']);
?>


