<?php
require_once "includes/config.php";
session_start();

// Check if user is logged in and is a customer
if(!isset($_SESSION["user_id"])){
    header("location: login.php");
    exit;
}

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
    <title>Government Schemes - AgroPlus</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 80px auto;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #2e7d32;
            margin-bottom: 30px;
            font-size: 2.5em;
        }

        .schemes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .scheme-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 20px;
            transition: transform 0.3s ease;
        }

        .scheme-card:hover {
            transform: translateY(-5px);
        }

        .scheme-title {
            font-size: 1.5em;
            color: #2e7d32;
            margin-bottom: 15px;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }

        .scheme-description {
            color: #666;
            line-height: 1.6;
            margin-bottom: 15px;
            max-height: 100px;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .scheme-description.expanded {
            max-height: none;
        }

        .read-more-btn {
            display: inline-block;
            padding: 8px 15px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 0.9em;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .read-more-btn:hover {
            background: #45a049;
        }

        .page-header {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }

        .page-description {
            color: #666;
            max-width: 800px;
            margin: 0 auto;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1>Government Schemes</h1>
            <p class="page-description">
                Explore various government schemes and benefits available for farmers to enhance productivity and income.
            </p>
        </div>

        <div class="schemes-grid">
            <?php while($scheme = mysqli_fetch_assoc($schemes)): ?>
                <div class="scheme-card">
                    <h2 class="scheme-title"><?php echo htmlspecialchars($scheme['name']); ?></h2>
                    <div class="scheme-description" id="desc-<?php echo $scheme['id']; ?>">
                        <?php echo htmlspecialchars($scheme['description']); ?>
                    </div>
                    <button class="read-more-btn" onclick="toggleDescription(<?php echo $scheme['id']; ?>)">Read More</button>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
        function toggleDescription(id) {
            const description = document.getElementById(`desc-${id}`);
            const button = description.nextElementSibling;
            
            if (description.classList.contains('expanded')) {
                description.classList.remove('expanded');
                button.textContent = 'Read More';
            } else {
                description.classList.add('expanded');
                button.textContent = 'Show Less';
            }
        }
    </script>
</body>
</html> 