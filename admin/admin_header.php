<?php
// Verify admin session
if (!isset($_SESSION['user_id'])) {
    header("Location:admin_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Admin Panel'; ?> - StudentMarketHub</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        /* Admin Header Styles */
        .admin-header {
            background-color: #2c3e50;
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }
        
        .admin-header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .admin-header .logo {
            display: flex;
            align-items: center;
        }
        
        .admin-header .logo img {
            height: 40px;
            margin-right: 10px;
        }
        
        .admin-header .logo span {
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .admin-nav ul {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .admin-nav li {
            margin-left: 20px;
        }
        
        .admin-nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        
        .admin-nav a i {
            margin-right: 8px;
            font-size: 0.9rem;
        }
        
        .admin-nav a:hover, 
        .admin-nav a.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: #4a6bff;
        }
        
        .admin-main {
            margin-top: 70px;
            padding: 20px;
        }
        
            /* Mobile Menu */
            .mobile-menu-btn {
                display: none;
                background: none;
                border: none;
                color: white;
                font-size: 1.5rem;
                cursor: pointer;
            }
            
        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }
            
            .admin-nav {
                position: fixed;
                top: 70px;
                left: -100%;
                width: 80%;
                height: calc(100vh - 70px);
                background-color: #2c3e50;
                transition: all 0.3s ease;
                padding: 20px;
                box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            }
            
            .admin-nav.active {
                left: 0;
            }
            
            .admin-nav ul {
                flex-direction: column;
            }
            
            .admin-nav li {
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="container">
                <div class="logo">
                    <a href="dashboard.php">
                    <img src="../uploads/logo.pro.png" alt="StudentMarketHub Logo">
                    <span>Admin Panel</span>
                    </a>
                </div>
            
            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="fas fa-bars"></i>
            </button>
            
            <nav class="admin-nav" id="adminNav">
                <ul>
                    <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="manage_products.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_products.php' ? 'active' : ''; ?>"><i class="fas fa-box"></i> Products</a></li>
                    <li><a href="manage_orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_orders.php' ? 'active' : ''; ?>"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li><a href="manage_users.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_users.php' ? 'active' : ''; ?>"><i class="fas fa-users"></i> Users</a></li>
                    <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <main class="admin-main">
        <div class="container">
            <!-- Page-specific content will be inserted here -->
            
            <!-- Success/Error Messages -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>

<script>
    // Mobile menu toggle
    document.getElementById('mobileMenuBtn').addEventListener('click', function() {
        document.getElementById('adminNav').classList.toggle('active');
    });
    
    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        const nav = document.getElementById('adminNav');
        const btn = document.getElementById('mobileMenuBtn');
        
        if (!nav.contains(event.target) && !btn.contains(event.target)) {
            nav.classList.remove('active');
        }
    });
</script>