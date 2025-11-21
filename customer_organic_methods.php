<?php
require_once "includes/config.php";
session_start();

// Check if user is logged in and is a customer
if(!isset($_SESSION["user_id"]) || $_SESSION["user_type"] != "customer"){
    header("location: login.php");
    exit;
}

// Fetch all organic methods content
$sql = "SELECT * FROM organic_methods_content ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

// Group content by method type
$methods = [];
while($row = mysqli_fetch_assoc($result)) {
    $methods[$row['method_type']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organic Methods - AgroPlus</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .organic-container {
            max-width: 1200px;
            margin: 80px auto 20px;
            padding: 20px;
        }

        .method-section {
            background: white;
            margin-bottom: 30px;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .method-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .method-card {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .method-card:hover {
            transform: translateY(-5px);
        }

        .method-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .method-title {
            color: #2e7d32;
            margin-bottom: 10px;
        }

        .method-description {
            color: #666;
            line-height: 1.6;
        }

        .section-title {
            color: #2e7d32;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #4CAF50;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            z-index: 1000;
        }

        .modal-content {
            position: relative;
            background: white;
            width: 90%;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            border-radius: 8px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .close-modal {
            position: absolute;
            right: 20px;
            top: 20px;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }

        .modal-image {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .modal-title {
            color: #2e7d32;
            margin-bottom: 15px;
            font-size: 1.5em;
        }

        .modal-description {
            line-height: 1.6;
            color: #333;
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .page-header h1 {
            color: #2e7d32;
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .page-header p {
            color: #666;
            font-size: 1.1em;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="organic-container">
        <div class="page-header">
            <h1>Organic Farming Methods</h1>
            <p>Learn about sustainable and eco-friendly farming practices</p>
        </div>

        <?php foreach($methods as $type => $contents): ?>
            <div class="method-section">
                <h2 class="section-title"><?php echo str_replace('_', ' ', ucfirst($type)); ?></h2>
                <div class="method-grid">
                    <?php foreach($contents as $content): ?>
                        <div class="method-card" onclick="openModal(<?php echo htmlspecialchars(json_encode($content)); ?>)">
                            <?php if($content['image_path']): ?>
                                <img src="<?php echo htmlspecialchars($content['image_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($content['title']); ?>">
                            <?php endif; ?>
                            <h3 class="method-title"><?php echo htmlspecialchars($content['title']); ?></h3>
                            <p class="method-description"><?php echo htmlspecialchars($content['description']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Modal -->
    <div id="contentModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <img id="modalImage" src="" alt="" class="modal-image">
            <h2 id="modalTitle" class="modal-title"></h2>
            <p id="modalDescription" class="modal-description"></p>
        </div>
    </div>

    <script>
        function openModal(content) {
            const modal = document.getElementById('contentModal');
            const modalImage = document.getElementById('modalImage');
            const modalTitle = document.getElementById('modalTitle');
            const modalDescription = document.getElementById('modalDescription');

            modalImage.src = content.image_path;
            modalTitle.textContent = content.title;
            modalDescription.textContent = content.description;

            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            const modal = document.getElementById('contentModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('contentModal');
            if (event.target == modal) {
                closeModal();
            }
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html> 