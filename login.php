<?php
// At the top of the file
require_once 'includes/email_functions.php';

// After successful login (before redirect)

?>

<?php
require_once 'includes/db_connect.php';
session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $errors[] = "Please enter both email and password.";
    } else {
        try {
            $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();

                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_role'] = $user['role'];
                    $login_success = true;
                    if ($login_success) {
                        // Send welcome back email (not on every login - maybe once per day)
                        $last_login = $_SESSION['last_login'] ?? null;
                        $today = date('Y-m-d');
                        
                        if ($last_login != $today) {
                            sendWelcomeBackEmail($email, $name);
                            $_SESSION['last_login'] = $today;
                        }
                        
                    }

                    header("Location: index.php");
                    exit;
                } else {
                    $errors[] = "Invalid password.";
                }
            } else {
                $errors[] = "No account found with this email.";
            }
        } catch (Exception $e) {
            $errors[] = "Login error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<hea>
   
    <title>Login - StudentMarketHub</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/login.css">
    <style>
        .astu_logo{
            margin-top:100px;
            margin-bottom:auto;
            max-width: 200px;           
            padding: 30px;
            background: white;           
        }
    </style>

</head>
<body class="login-page">
    <div class="login-container">
        <div class="university-brand">
            <img src="uploads\image.png" alt="ASTU LOGO" class="astu_logo" />
            <div class="university-name">ADAMA SCIENCE AND TECHNOLOGY UNIVERSITY</div>
            <div class="platform-name">Student MARKET HUB</div>
        </div>
        
        <h2>Login to Your Account</h2>
        
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" class="login-form">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required placeholder="Enter your email">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter your password">
            </div>
            
            <button type="submit" class="login-btn">Login <i class="fas fa-sign-in-alt"></i></button>
        </form>
        
        <div class="login-links">
            <a href="forgot-password.php"><i class="fas fa-key"></i> Forgot Password?</a>
            <a href="register.php"><i class="fas fa-user-plus"></i> Create Account</a>
        </div>
    </div>
</body>
</html>