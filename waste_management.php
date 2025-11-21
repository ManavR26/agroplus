<?php
require_once "includes/config.php";
session_start();

// Fetch all waste management content
$sql = "SELECT * FROM waste_management_content ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waste Management</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .hero-section {
            background: #f9f9f9;
            padding: 40px 20px;
            text-align: center;
            margin-top: 60px;
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .hero-content h1 {
            color: #2e7d32;
            font-size: 2.5em;
            margin-bottom: 15px;
        }

        .hero-content p {
            color: #666;
            font-size: 1.2em;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .content-section {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }

        .content-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .content-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .content-card:hover {
            transform: translateY(-3px);
        }

        .content-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .content-info {
            padding: 15px;
        }

        .content-info h2 {
            color: #2e7d32;
            margin-bottom: 8px;
            font-size: 1.2em;
        }

        .content-info p {
            color: #666;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            font-size: 0.95em;
        }

        .read-more {
            display: inline-block;
            color: #4CAF50;
            margin-top: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 0.9em;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            z-index: 1100;
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
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="hero-section">
        <div class="hero-content">
            <h1>Waste Management</h1>
            <p>Learn about efficient agricultural waste management techniques and sustainable farming practices.</p>
        </div>
    </div>

    <div class="content-section">
        <div class="content-grid">
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <div class="content-card" onclick="openModal(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                    <?php if($row['image']): ?>
                        <img src="uploads/waste_management/<?php echo htmlspecialchars($row['image']); ?>" 
                             alt="<?php echo htmlspecialchars($row['title']); ?>">
                    <?php endif; ?>
                    <div class="content-info">
                        <h2><?php echo htmlspecialchars($row['title']); ?></h2>
                        <p><?php echo htmlspecialchars($row['description']); ?></p>
                        <span class="read-more">Read More →</span>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
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

            modalImage.src = 'uploads/waste_management/' + content.image;
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