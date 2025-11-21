<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If logged in, send users to their proper dashboard instead of staying on home
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'customer') {
        header('Location: customer_dashboard.php');
        exit;
    }
    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'farmer') {
        header('Location: categories.php');
        exit;
    }
}

// Update last activity to prevent session timeout
if (isset($_SESSION['user_id'])) {
    $_SESSION['last_activity'] = time();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgroPlus</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            background: url('assets/images/irewolede-PvwdlXqo85k-unsplash.jpg') no-repeat center center fixed;
            background-size: cover;
            position: relative;
            background-color: rgba(255, 248, 220, 0.1);
            background-blend-mode: overlay;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(
                rgba(0, 0, 0, 0.2),
                rgba(0, 0, 0, 0.3)
            );
            z-index: 0;
        }

        .navbar {
            background: #2f2f2f;
            border-bottom: 1px solid #404040;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 0 20px;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 0.8rem 0;
            position: relative;
        }

        .navbar-left {
            position: absolute;
            left: -140px;
            font-family: 'Arial Black', 'Arial Bold', sans-serif;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .agroplus-logo {
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            transition: transform 0.3s ease;
        }

        .agroplus-logo:hover .logo-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .navbar-left a {
            font-size: 28px;
            font-weight: bold;
            color: #4CAF50; /* Green color for logo text */
            text-decoration: none;
            letter-spacing: 1px;
            text-transform: none;
            line-height: 1;
            background: linear-gradient(45deg, #4CAF50, #8BC34A);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .navbar-center {
            display: flex;
            justify-content: flex-start;
            gap: 1rem;
            margin-left: auto;
            margin-right: -140px;
        }

        .navbar-right {
            display: flex;
            justify-content: flex-end;
            gap: 2rem;
            align-items: center;
            margin-left: 80px;
            height: 100%;
            position: relative;
            right: -80px;
        }

        .nav-link {
            color: #e0e0e0;
            text-decoration: none;
            font-size: 16px;
            padding: 13px 10px;  /* Increased vertical padding to match new height */
            border-radius: 4px;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background-color: #ffffff;
            transition: all 0.3s ease;
        }

        .nav-link:hover:after {
            width: 100%;
            left: 0;
        }

        .nav-link:hover {
            color: #ffffff;
        }

        .nav-link.active {
            color: #ffffff;
            font-weight: bold;
        }

        .auth-buttons {
            display: flex;
            gap: 1rem;
            margin-right: 0;
        }

        .login-btn, .register-btn {
            padding: 8px 20px;
            border-radius: 4px;
            text-decoration: none;
            transition: all 0.3s ease;
            background: #404040; /* Darker silver for buttons */
            color: white;
        }

        .login-btn:hover, .register-btn:hover {
            background: #505050; /* Slightly lighter on hover */
        }

        .user-menu {
            position: relative;
            height: 100%;
            display: flex;
            align-items: center;
            margin-right: -30px;
        }

        .user-menu > .nav-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 15px;
            cursor: pointer;
            height: 100%;
            color: #e0e0e0;
            font-size: 14px;
            white-space: nowrap;
        }

        .user-menu-content {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            background: #2f2f2f;
            min-width: 250px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.3);
            border-radius: 4px;
            padding: 10px 0;
            margin-top: 0;
            z-index: 1001;
        }

        .user-menu-content a {
            padding: 12px 20px;
            display: block;
            color: #e0e0e0;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .user-menu-content a:hover {
            background: #404040;
        }

        .user-menu:hover .user-menu-content {
            display: block;
        }

        .user-menu > .nav-link span {
            font-size: 12px;
            margin-left: 5px;
            transition: transform 0.3s;
        }

        .user-menu:hover > .nav-link span {
            transform: rotate(180deg);
        }

        .profile-preview {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-decoration: none;
        }

        .profile-preview:hover {
            background: #404040;
        }

        .profile-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: #E0E0E0;
            overflow: hidden;
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-info {
            flex: 1;
        }

        .profile-info .username {
            color: #ffffff;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 3px;
        }

        .profile-info .view-profile {
            color: #999;
            font-size: 11px;
        }

        /* Main content padding to account for fixed navbar */
        main {
            padding-top: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 1;
        }

        /* Rest of your existing styles */
        .hero-section {
            text-align: center;
            padding: 0 20px;
            background: transparent;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2rem;
        }

        .hero-logo {
            width: 120px;
            height: 120px;
            animation: float 3s ease-in-out infinite;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .hero-section h1 {
            font-size: 3.5em;
            color: #ffffff; /* High-contrast on photo background */
            text-shadow: 0 2px 6px rgba(0,0,0,0.4);
            font-weight: bold;
            letter-spacing: 2px;
            margin: 0;
        }

        .hero-subtitle {
            font-size: 1.2em;
            color: #ffffff; /* high contrast over photo */
            text-shadow: 0 2px 6px rgba(0,0,0,0.5);
            margin: -10px 0 40px 0; /* Reduced bottom margin by 60px */
            font-style: italic;
            font-weight: bold; /* Bold text */
            background: rgba(0,0,0,0.25);
            padding: 6px 12px;
            border-radius: 8px;
            display: inline-block;
        }

        .hero-buttons {
            display: flex;
            gap: 1.5rem;
            margin-top: -40px; /* Reduced top margin by 60px */
        }

        .hero-btn {
            padding: 12px 35px;
            border-radius: 5px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 1.1em;
            font-weight: bold;
            text-transform: uppercase;
            border: 2px solid #404040;
        }

        .login-hero-btn {
            background: #2f2f2f;
            color: white;
        }

        .register-hero-btn {
            background: #404040;
            color: white;
        }

        .hero-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            background: #505050;
            border-color: #505050;
        }

        .user-menu-link {
            display: block;
            padding: 12px 20px;
            color: #e0e0e0;
            text-decoration: none;
            transition: background 0.3s;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .user-menu-link:last-child {
            border-bottom: none;
        }

        .user-menu-link:hover {
            background: #404040;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="navbar-left">
                <a href="index.php" class="agroplus-logo">
                    <svg class="logo-icon" viewBox="0 0 50 50" xmlns="http://www.w3.org/2000/svg">
                        <!-- Cute plant with leaves -->
                        <circle cx="25" cy="40" r="8" fill="#8D6E63" stroke="#5D4037" stroke-width="1"/> <!-- Pot -->
                        <rect x="22" y="32" width="6" height="12" fill="#4CAF50" rx="3"/> <!-- Stem -->
                        
                        <!-- Main leaves -->
                        <ellipse cx="18" cy="28" rx="6" ry="4" fill="#66BB6A" transform="rotate(-30 18 28)"/>
                        <ellipse cx="32" cy="28" rx="6" ry="4" fill="#66BB6A" transform="rotate(30 32 28)"/>
                        
                        <!-- Top leaves -->
                        <ellipse cx="20" cy="20" rx="5" ry="3" fill="#81C784" transform="rotate(-45 20 20)"/>
                        <ellipse cx="30" cy="20" rx="5" ry="3" fill="#81C784" transform="rotate(45 30 20)"/>
                        
                        <!-- Center flower/fruit -->
                        <circle cx="25" cy="18" r="4" fill="#FF9800"/>
                        <circle cx="25" cy="18" r="2" fill="#FFC107"/>
                        
                        <!-- Cute sun rays -->
                        <line x1="25" y1="8" x2="25" y2="12" stroke="#FDD835" stroke-width="2" stroke-linecap="round"/>
                        <line x1="35" y1="10" x2="33" y2="12" stroke="#FDD835" stroke-width="2" stroke-linecap="round"/>
                        <line x1="15" y1="10" x2="17" y2="12" stroke="#FDD835" stroke-width="2" stroke-linecap="round"/>
                        
                        <!-- Small decorative dots -->
                        <circle cx="12" cy="35" r="1.5" fill="#AED581"/>
                        <circle cx="38" cy="35" r="1.5" fill="#AED581"/>
                        <circle cx="8" cy="25" r="1" fill="#C8E6C9"/>
                        <circle cx="42" cy="25" r="1" fill="#C8E6C9"/>
                    </svg>
                    <span>AgroPlus</span>
                </a>
            </div>
            
            <div class="navbar-center">
                <a href="index.php" class="nav-link <?php echo ($_SERVER['PHP_SELF'] == '/index.php' ? 'active' : ''); ?>">Home</a>
                <a href="contact.php" class="nav-link <?php echo ($_SERVER['PHP_SELF'] == '/contact.php' ? 'active' : ''); ?>">Contact us</a>
                <a href="about.php" class="nav-link <?php echo ($_SERVER['PHP_SELF'] == '/about.php' ? 'active' : ''); ?>">About us</a>
            </div>
            
            <div class="navbar-right">
                <?php if(isset($_SESSION["user_id"])): ?>
                    <div class="user-menu">
                        <div class="nav-link">
                            <div class="profile-avatar">
                                <img src="assets/images/default-avatar.png" alt="Profile">
                            </div>
                            <span><?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                        </div>
                        <div class="user-menu-content">
                            <a href="profile.php" class="profile-preview">
                                <div class="profile-avatar">
                                    <img src="assets/images/default-avatar.png" alt="Profile">
                                </div>
                                <div class="profile-info">
                                    <div class="username"><?php echo htmlspecialchars($_SESSION["username"]); ?></div>
                                    <div class="view-profile">View Profile</div>
                                </div>
                            </a>
                            <?php if($_SESSION["user_type"] == "farmer"): ?>
                                <a href="upload_product.php">Upload Product</a>
                                <a href="view_products.php">View My Products</a>
                            <?php else: ?>
                                <a href="products.php">Products</a>
                                <a href="cart.php">Cart</a>
                            <?php endif; ?>
                            <a href="logout.php">Logout</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main>
        <div class="hero-section">
            <!-- Large Hero Logo -->
            <svg class="hero-logo" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <!-- Cute farm scene -->
                <circle cx="50" cy="80" r="15" fill="#8D6E63" stroke="#5D4037" stroke-width="2"/> <!-- Pot -->
                <rect x="45" y="65" width="10" height="20" fill="#4CAF50" rx="5"/> <!-- Main stem -->
                
                <!-- Large main leaves -->
                <ellipse cx="30" cy="55" rx="12" ry="8" fill="#66BB6A" transform="rotate(-30 30 55)"/>
                <ellipse cx="70" cy="55" rx="12" ry="8" fill="#66BB6A" transform="rotate(30 70 55)"/>
                
                <!-- Secondary leaves -->
                <ellipse cx="35" cy="40" rx="10" ry="6" fill="#81C784" transform="rotate(-45 35 40)"/>
                <ellipse cx="65" cy="40" rx="10" ry="6" fill="#81C784" transform="rotate(45 65 40)"/>
                
                <!-- Top leaves -->
                <ellipse cx="40" cy="25" rx="8" ry="5" fill="#A5D6A7" transform="rotate(-60 40 25)"/>
                <ellipse cx="60" cy="25" rx="8" ry="5" fill="#A5D6A7" transform="rotate(60 60 25)"/>
                
                <!-- Beautiful flower at top -->
                <circle cx="50" cy="20" r="8" fill="#FF9800"/>
                <circle cx="50" cy="20" r="5" fill="#FFC107"/>
                <circle cx="50" cy="20" r="2" fill="#FF5722"/>
                
                <!-- Cute sun with face -->
                <circle cx="80" cy="20" r="8" fill="#FDD835"/>
                <!-- Sun rays -->
                <line x1="80" y1="8" x2="80" y2="12" stroke="#F57F17" stroke-width="2" stroke-linecap="round"/>
                <line x1="88" y1="12" x2="86" y2="14" stroke="#F57F17" stroke-width="2" stroke-linecap="round"/>
                <line x1="88" y1="20" x2="84" y2="20" stroke="#F57F17" stroke-width="2" stroke-linecap="round"/>
                <line x1="88" y1="28" x2="86" y2="26" stroke="#F57F17" stroke-width="2" stroke-linecap="round"/>
                <line x1="80" y1="32" x2="80" y2="28" stroke="#F57F17" stroke-width="2" stroke-linecap="round"/>
                <line x1="72" y1="28" x2="74" y2="26" stroke="#F57F17" stroke-width="2" stroke-linecap="round"/>
                <line x1="72" y1="20" x2="76" y2="20" stroke="#F57F17" stroke-width="2" stroke-linecap="round"/>
                <line x1="72" y1="12" x2="74" y2="14" stroke="#F57F17" stroke-width="2" stroke-linecap="round"/>
                
                <!-- Sun face -->
                <circle cx="77" cy="18" r="1" fill="#333"/> <!-- Left eye -->
                <circle cx="83" cy="18" r="1" fill="#333"/> <!-- Right eye -->
                <path d="M 76 22 Q 80 25 84 22" stroke="#333" stroke-width="1.5" fill="none" stroke-linecap="round"/> <!-- Smile -->
                
                <!-- Small butterflies -->
                <g transform="translate(15,30)">
                    <ellipse cx="0" cy="0" rx="3" ry="2" fill="#E91E63" transform="rotate(-20)"/>
                    <ellipse cx="2" cy="0" rx="3" ry="2" fill="#F06292" transform="rotate(20)"/>
                    <line x1="1" y1="-2" x2="1" y2="2" stroke="#333" stroke-width="0.5"/>
                </g>
                
                <!-- Decorative grass -->
                <path d="M 10 90 Q 15 85 20 90" stroke="#4CAF50" stroke-width="2" fill="none"/>
                <path d="M 25 90 Q 30 85 35 90" stroke="#4CAF50" stroke-width="2" fill="none"/>
                <path d="M 65 90 Q 70 85 75 90" stroke="#4CAF50" stroke-width="2" fill="none"/>
                <path d="M 80 90 Q 85 85 90 90" stroke="#4CAF50" stroke-width="2" fill="none"/>
            </svg>
            
            <h1>Welcome to AgroPlus</h1>
            <p class="hero-subtitle">🌱 Your Gateway to Fresh, Organic Farming 🌱</p>
            <?php if(!isset($_SESSION["user_id"])): ?>
                <div class="hero-buttons">
                    <a href="login.php" class="hero-btn login-hero-btn">🚀 Login</a>
                    <a href="register.php" class="hero-btn register-hero-btn">🌟 Sign Up</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script src="assets/js/main.js"></script>
</body>
</html> 