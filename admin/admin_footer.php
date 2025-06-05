        </div> <!-- Close container div from admin_header.php -->
    </main>

    <footer class="admin-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <img src="../uploads/logo.pro.png" alt="StudentMarketHub Logo" class="footer-logo">
                    <span>StudentMarketHub Admin Panel</span>
                </div>
                
                <div class="footer-links">
                    <div class="footer-column">
                        <h4>Quick Links</h4>
                        <ul>
                            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                            <li><a href="manage_products.php"><i class="fas fa-box"></i> Products</a></li>
                            <li><a href="manage_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                        </ul>
                    </div>
                    
                    <div class="footer-column">
                        <h4>System</h4>
                        <ul>
                            <li><a href="system_settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                            <li><a href="admin_profile.php"><i class="fas fa-user-cog"></i> Profile</a></li>
                            <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </div>
                    
                    <div class="footer-column">
                        <h4>Help</h4>
                        <ul>
                            <li><a href="help.php"><i class="fas fa-question-circle"></i> Documentation</a></li>
                            <li><a href="contact_support.php"><i class="fas fa-headset"></i> Support</a></li>
                            <li><a href="report_issue.php"><i class="fas fa-bug"></i> Report Issue</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> StudentMarketHub. All Rights Reserved.</p>
                <div class="footer-social">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-telegram"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <style>
        /* Admin Footer Styles */
        .admin-footer {
            background-color: #2c3e50;
            color: white;
            padding: 40px 0 20px;
            margin-top: 40px;
        }
        
        .admin-footer .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .footer-content {
            display: flex;
            flex-wrap: wrap;
            gap: 40px;
            margin-bottom: 30px;
        }
        
        .footer-brand {
            flex: 1;
            min-width: 250px;
        }
        
        .footer-logo {
            height: 40px;
            margin-bottom: 15px;
        }
        
        .footer-brand span {
            display: block;
            font-size: 1.1rem;
            font-weight: 600;
            margin-top: 10px;
        }
        
        .footer-links {
            flex: 2;
            display: flex;
            flex-wrap: wrap;
            gap: 40px;
        }
        
        .footer-column {
            min-width: 150px;
        }
        
        .footer-column h4 {
            color: #4a6bff;
            margin-bottom: 20px;
            font-size: 1.1rem;
        }
        
        .footer-column ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .footer-column li {
            margin-bottom: 12px;
        }
        
        .footer-column a {
            color: #adb5bd;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .footer-column a:hover {
            color: white;
        }
        
        .footer-column i {
            width: 20px;
            text-align: center;
        }
        
        .footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .footer-bottom p {
            margin: 0;
            color: #adb5bd;
            font-size: 0.9rem;
        }
        
        .footer-social {
            display: flex;
            gap: 15px;
        }
        
        .footer-social a {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .footer-social a:hover {
            background-color: #4a6bff;
            transform: translateY(-3px);
        }
        
        /* Responsive Styles */
        @media (max-width: 768px) {
            .footer-content {
                flex-direction: column;
                gap: 30px;
            }
            
            .footer-links {
                flex-direction: column;
                gap: 30px;
            }
            
            .footer-bottom {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/admin.js"></script>
    
    <!-- Page-specific scripts -->
    <?php if (isset($footer_scripts)): ?>
        <?php foreach ($footer_scripts as $script): ?>
            <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>