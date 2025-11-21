<?php
require_once "includes/config.php";
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - AgroPlus</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background: url('assets/images/jed-owen-ajZibDGpPew-unsplash.jpg') center center / cover no-repeat fixed;
        }

        /* Soft dark overlay for better contrast on any background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: linear-gradient(180deg, rgba(0,0,0,0.25), rgba(0,0,0,0.35));
            pointer-events: none;
            z-index: 0;
        }

        .about-container {
            max-width: 1000px;
            margin: 80px auto 20px;
            padding: 30px;
            background: rgba(255,255,255,0.92);
            border-radius: 0;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            backdrop-filter: saturate(130%) blur(2px);
            position: relative;
            z-index: 1;
        }

        .about-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .about-header h1 {
            color: #2e7d32;
            font-size: 2.5em;
            margin-bottom: 15px;
        }

        .about-header p {
            color: #666;
            font-size: 1.1em;
            max-width: 800px;
            margin: 0 auto;
        }

        .about-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin: 40px 0;
        }

        .about-card {
            background: rgba(249,249,249,0.95);
            padding: 25px;
            border-radius: 0;
            text-align: center;
            transition: none;
        }

        .about-card:hover {
            transform: none;
        }

        .about-card i {
            font-size: 40px;
            color: #4CAF50;
            margin-bottom: 20px;
        }

        .about-card h3 {
            color: #2e7d32;
            margin-bottom: 15px;
            font-size: 1.3em;
        }

        .about-card p {
            color: #666;
            line-height: 1.6;
        }

        .mission-section {
            background: rgba(245,245,245,0.95);
            padding: 30px;
            border-radius: 0;
            margin: 40px 0;
        }

        .mission-section h2 {
            color: #2e7d32;
            margin-bottom: 20px;
            text-align: center;
        }

        .mission-section p {
            color: #444;
            line-height: 1.8;
            margin-bottom: 15px;
        }

        .team-section {
            text-align: center;
            margin: 40px 0;
        }

        .team-section h2 {
            color: #2e7d32;
            margin-bottom: 30px;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-top: 40px;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }

        .team-member {
            background: rgba(249,249,249,0.95);
            padding: 20px;
            text-align: center;
        }

        .team-member.leader {
            grid-column: 1 / -1;
            background: rgba(245,245,245,0.95);
            padding: 30px;
            margin-bottom: 20px;
        }

        .team-member h3 {
            color: #2e7d32;
            margin-bottom: 8px;
        }

        .team-member p {
            color: #666;
        }

        @media (max-width: 768px) {
            .team-grid {
                grid-template-columns: repeat(1, 1fr);
                gap: 20px;
                padding: 0 20px;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="about-container">
        <div class="about-header">
            <h1>About AgroPlus</h1>
            <p>Connecting farmers and consumers through sustainable agriculture</p>
        </div>

        <div class="about-grid">
            <div class="about-card">
                <i class="fas fa-seedling"></i>
                <h3>Our Vision</h3>
                <p>To create a sustainable agricultural ecosystem that benefits both farmers and consumers while promoting organic farming practices.</p>
            </div>
            <div class="about-card">
                <i class="fas fa-users"></i>
                <h3>Community</h3>
                <p>Building a strong community of farmers and consumers who believe in sustainable and organic farming methods.</p>
            </div>
            <div class="about-card">
                <i class="fas fa-leaf"></i>
                <h3>Sustainability</h3>
                <p>Promoting eco-friendly farming practices and helping farmers transition to organic methods.</p>
            </div>
            <div class="about-card">
                <i class="fas fa-lightbulb"></i>
                <h3>Innovation</h3>
                <p>Implementing modern agricultural technologies and smart farming solutions to enhance productivity and efficiency.</p>
            </div>
        </div>

        <div class="mission-section">
            <h2>Our Mission</h2>
            <p>At AgroPlus, we are dedicated to revolutionizing the agricultural sector by providing a direct platform for farmers to connect with consumers. Our mission is to promote sustainable farming practices while ensuring fair prices for farmers and quality products for consumers.</p>
            <p>We believe in:</p>
            <ul style="margin-left: 20px; line-height: 1.6; color: #444;">
                <li>Supporting local farmers and their communities</li>
                <li>Promoting organic and sustainable farming practices</li>
                <li>Ensuring fair prices for agricultural products</li>
                <li>Providing education about sustainable agriculture</li>
                <li>Building a transparent and efficient supply chain</li>
            </ul>
        </div>

        <div class="team-section">
            <h2>Our Team</h2>
            <div class="team-grid">
                <!-- Leader -->
                <div class="team-member leader">
                    <h3>Abhi Mengar</h3>
                    <p>Team Leader</p>
                </div>
                
                <!-- Team Members -->
                <div class="team-member">
                    <h3>Aniket Kharwar</h3>
                    <p>Team Member</p>
                </div>
                <div class="team-member">
                    <h3>Manav Rathva</h3>
                    <p>Team Member</p>
                </div>
                <div class="team-member">
                    <h3>Nistha Parmar</h3>
                    <p>Team Member</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</body>
</html> 