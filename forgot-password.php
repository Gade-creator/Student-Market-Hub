<?php
require_once 'includes/db_connect.php';
session_start();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $error = "Please enter your email address";
    } else {
        try {
            // Check if email exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                // Generate reset token (in a real app, you would send this via email)
                $token = bin2hex(random_bytes(32));
                $expires = date("Y-m-d H:i:s", time() + 3600); // 1 hour expiration
                
                // Store token in database
                $update_stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
                $update_stmt->bind_param("sss", $token, $expires, $email);
                $update_stmt->execute();
                
                // In production, you would send an email here with reset link
                $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/reset-password.php?token=$token";
                $message = "Password reset link has been sent to your email (simulated).";
                
                // For demo purposes only - remove in production
                $message .= "<div class='demo-reset-link'>DEMO ONLY: <a href='$reset_link'>$reset_link</a></div>";
            } else {
                $error = "No account found with that email address";
            }
        } catch (Exception $e) {
            $error = "Error processing request: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password - StudentMarketHub</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/login.css"> <!-- Reusing login page styles -->
    <style>
        .forgot-password-container {
            max-width: 500px;
        }
        .instructions {
            color: #6c757d;
            margin-bottom: 20px;
            text-align: center;
        }
        .demo-reset-link {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            font-size: 14px;
            word-break: break-all;
        }
        .demo-reset-link a {
            color: #4a6bff;
        }
    </style>
</head>
<body class="login-page">
    <div class="login-container forgot-password-container">
        <div class="university-brand">
            <div class="university-name">ADAMA SCIENCE AND TECHNOLOGY UNIVERSITY</div>
            <div class="platform-name">Student MARKET HUB</div>
        </div>
        
        <h2>Reset Your Password</h2>
        
        <?php if ($error): ?>
            <div class="error-message">
                <p><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></p>
            </div>
        <?php endif; ?>
        
        <?php if ($message): ?>
            <div class="success-message" style="color: #28a745; background-color: rgba(40, 167, 69, 0.1); padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #28a745;">
                <p><i class="fas fa-check-circle"></i> <?php echo $message; ?></p>
            </div>
        <?php else: ?>
            <p class="instructions">Enter your email address and we'll send you a link to reset your password.</p>
            
            <form method="POST" action="" class="login-form">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your registered email">
                </div>
                
                <button type="submit" class="login-btn">Send Reset Link <i class="fas fa-paper-plane"></i></button>
            </form>
        <?php endif; ?>
        
        <div class="login-links">
            <a href="login.php"><i class="fas fa-sign-in-alt"></i> Back to Login</a>
            <a href="register.php"><i class="fas fa-user-plus"></i> Create New Account</a>
        </div>
    </div>
</body>
</html>