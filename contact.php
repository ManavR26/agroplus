<?php
require_once "includes/config.php";
session_start();
// Prevent cached view so that after logout Back doesn't show this page
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - AgroPlus</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <style>
        body {
            background: url('assets/images/jed-owen-ajZibDGpPew-unsplash.jpg') center center / cover no-repeat fixed;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: linear-gradient(180deg, rgba(0,0,0,0.25), rgba(0,0,0,0.35));
            pointer-events: none;
            z-index: 0;
        }

        .contact-container {
            max-width: 800px;
            margin: 80px auto 20px;
            padding: 30px;
            background: rgba(255,255,255,0.92);
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            backdrop-filter: saturate(130%) blur(2px);
            position: relative;
            z-index: 1;
        }

        .contact-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .contact-header h1 {
            color: #2e7d32;
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .contact-header p {
            color: #666;
            font-size: 1.1em;
        }

        .contact-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
            text-align: center;
        }

        .info-item {
            padding: 20px;
            background: rgba(249,249,249,0.95);
            border-radius: 8px;
            transition: transform 0.3s ease;
        }

        .info-item:hover {
            transform: translateY(-5px);
        }

        .info-item i {
            font-size: 32px;
            color: #4CAF50;
            margin-bottom: 15px;
        }

        .info-item h3 {
            color: #2e7d32;
            margin-bottom: 10px;
        }

        .info-item p {
            color: #666;
            line-height: 1.5;
        }

        .map-container {
            margin-top: 20px;
            border-radius: 8px;
            overflow: hidden;
            height: 400px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .map-container iframe {
            width: 100%;
            height: 100%;
            border: 0;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="contact-container">
        <div class="contact-header">
            <h1>Contact Us</h1>
            <p>Get in touch with us for any inquiries or support</p>
        </div>

        <div class="contact-info">
            <div class="info-item">
                <i class="fas fa-map-marker-alt"></i>
                <h3>Address</h3>
                <p>123 Agro Street<br>Farming District<br>State - 123456</p>
            </div>
            <div class="info-item">
                <i class="fas fa-phone"></i>
                <h3>Phone</h3>
                <p>+91 1234567890<br>+91 9876543210</p>
            </div>
            <div class="info-item">
                <i class="fas fa-envelope"></i>
                <h3>Email</h3>
                <p>contact@agroplus.com<br>support@agroplus.com</p>
            </div>
        </div>

        <div class="map-container">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d118147.68401229884!2d73.12351229883556!3d22.32200405969978!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x395fc8ab91a3ddab%3A0xac39d3bfe1473fb8!2sVadodara%2C%20Gujarat!5e0!3m2!1sen!2sin!4v1709799611099!5m2!1sen!2sin" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</body>
</html> 