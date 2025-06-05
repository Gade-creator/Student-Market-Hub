<?php
// Email configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your@gmail.com'); // Your Gmail address
define('SMTP_PASSWORD', 'your-app-password'); // Use App Password, not your regular password
define('SMTP_FROM', 'your@gmail.com');
define('SMTP_FROM_NAME', 'StudentMarketHub');
define('SMTP_SECURE', 'tls');
define('SMTP_AUTH', true);
define('SMTP_DEBUG', 0); // 0 for production, 2 for debugging