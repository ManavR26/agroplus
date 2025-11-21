<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Update last activity to prevent session timeout
if (isset($_SESSION['user_id'])) {
    $_SESSION['last_activity'] = time();
}

// Set current page variable
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<nav class="navbar">
    <div class="nav-container">
        <!-- AgroPlus Logo and Name -->
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
        
        <div class="nav-items">
            <?php if(isset($_SESSION["user_id"]) && $_SESSION["user_type"] == "customer"): ?>
                <a href="index.php" class="<?php echo ($current_page == 'index') ? 'active' : ''; ?>">Home</a>
            <?php else: ?>
                <a href="index.php" class="<?php echo ($current_page == 'index') ? 'active' : ''; ?>">Home</a>
            <?php endif; ?>
            
            <?php if(isset($_SESSION["user_id"])): ?>
                <!-- Profile/Username section for logged in users -->
                <a href="profile.php" class="username">
                    👤 <?php echo htmlspecialchars($_SESSION["username"]); ?>
                </a>
                
                <!-- Dashboard links based on user type -->
                <?php if($_SESSION["user_type"] == "admin"): ?>
                    <a href="admin_dashboard.php">Admin Dashboard</a>
                <?php elseif($_SESSION["user_type"] == "farmer"): ?>
                    <a href="farmer_dashboard.php">Farmer Dashboard</a>
                <?php elseif($_SESSION["user_type"] == "customer"): ?>
                    <a href="customer_dashboard.php">Customer Dashboard</a>
                <?php endif; ?>
                
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <?php if($current_page != 'login'): ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
                <?php if($current_page != 'register'): ?>
                    <a href="register.php">Sign Up</a>
                <?php endif; ?>
                <a href="admin_login.php">Admin Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<style>
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
        justify-content: space-between;
        align-items: center;
        padding: 0.8rem 0;
        position: relative;
    }

    .navbar-left {
        display: flex;
        align-items: center;
        gap: 10px;
        position: absolute;
        left: -130px; /* Fixed positioning */
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

    .agroplus-logo span {
        font-size: 28px;
        font-weight: bold;
        color: #4CAF50;
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

    .nav-items {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 20px;
        flex: 1;
        margin-left: 0; /* Reset margin to center properly */
        margin-right: 0;
    }

    .nav-items a {
        color: #e0e0e0;
        text-decoration: none;
        font-size: 16px;
        padding: 8px 20px;
        border-radius: 4px;
        transition: all 0.3s ease;
        position: relative;
    }

    .nav-items a:after {
        content: '';
        position: absolute;
        width: 0;
        height: 2px;
        bottom: 0;
        left: 50%;
        background-color: #ffffff;
        transition: all 0.3s ease;
    }

    .nav-items a:hover:after {
        width: 100%;
        left: 0;
    }

    .nav-items a:hover {
        color: #ffffff;
    }

    .username {
        color: #ffffff !important;
        font-size: 16px !important;
        padding: 8px 15px !important;
        background: rgba(255,255,255,0.1) !important;
        border-radius: 4px !important;
        text-decoration: none !important;
        transition: all 0.3s ease !important;
    }

    .username:hover {
        background: rgba(255,255,255,0.2) !important;
    }

    .active {
        color: #ffffff !important;
        font-weight: bold;
    }

    .active:after {
        width: 100% !important;
        left: 0 !important;
    }
</style> 