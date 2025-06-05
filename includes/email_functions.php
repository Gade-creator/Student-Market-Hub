<?php
require_once 'email_config.php';
require_once __DIR__ . '/../vendor/autoload.php'; // Composer autoload


function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = SMTP_AUTH;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port       = SMTP_PORT;
        $mail->SMTPDebug  = SMTP_DEBUG;

        // Recipients
        $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
function sendWelcomeEmail($email, $name) {
    require 'vendor/autoload.php';
    
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Example for Gmail
        $mail->SMTPAuth   = true;
        $mail->Username   = 'gadisagutema943@gmail.com'; // Your Gmail
        $mail->Password   = 'npip clay wyvj rbas'; // Use App Password
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;
        
        // Recipients
        $mail->setFrom('your@gmail.com', 'StudentMarketHub');
        $mail->addAddress($email, $name);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Welcome to StudentMarketHub!';
        $mail->Body    = "Dear $name,<br><br>Welcome to StudentMarketHub!...";
        
        $mail->send();
        error_log("DEBUG: Email sent to $email"); // Add this line
        return true;
    } catch (Exception $e) {
        error_log("Email Error: " . $e->getMessage());
        return false;
    }
    $subject = "Welcome to StudentMarketHub!";
    $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .header { color: #4a6bff; font-size: 24px; margin-bottom: 20px; }
                .content { margin: 20px 0; }
                .footer { margin-top: 30px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='header'>Welcome to StudentMarketHub, $name!</div>
            <div class='content'>
                <p>Thank you for joining our community of buyers and sellers.</p>
                <p>You can now:</p>
                <ul>
                    <li>Browse products from fellow students</li>
                    <li>Sell your own items easily</li>
                    <li>Connect with other ASTU students</li>
                </ul>
                <p>We're excited to have you on board!</p>
            </div>
            <div class='footer'>
                <p>If you didn't create an account with us, please ignore this email.</p>
                <p>&copy; ".date('Y')." StudentMarketHub. All Rights Reserved.</p>
            </div>
        </body>
        </html>
    ";
    
    return sendEmail($email, $subject, $body);
}

function sendWelcomeBackEmail($email, $name) {
    $subject = "Welcome back to StudentMarketHub!";
    $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .header { color: #4a6bff; font-size: 24px; margin-bottom: 20px; }
                .content { margin: 20px 0; }
                .footer { margin-top: 30px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='header'>Welcome back, $name!</div>
            <div class='content'>
                <p>We're glad to see you again on StudentMarketHub.</p>
                <p>Here's what's new since your last visit:</p>
                <ul>
                    <li>New products added by sellers</li>
                    <li>Special discounts on selected items</li>
                    <li>Improved user experience</li>
                </ul>
                <p>Happy shopping/selling!</p>
            </div>
            <div class='footer'>
                <p>If you didn't log in to your account, please secure your account immediately.</p>
                <p>&copy; ".date('Y')." StudentMarketHub. All Rights Reserved.</p>
            </div>
        </body>
        </html>
    ";
    
    return sendEmail($email, $subject, $body);
}
?>