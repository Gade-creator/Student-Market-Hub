<?php
// At the top of the file
require_once("includes/email_functions.php");

// After successful registration (before redirect)

?>

<?php
include("includes/db_connect.php");

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $password = $_POST['password'];
    $role     = $_POST['role'];

    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $errors[] = "All fields marked * are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    // If no errors, proceed
    if (empty($errors)) {
        try {
            // Check for existing email
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $errors[] = "Email already registered.";
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert new user
                $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $name, $email, $phone, $hashed_password, $role);
                $stmt->execute();

                $success = "Registration successful! You can now <a href='login.php'>login</a>.";
                $success= true;
                if ($success) {
                    // Send welcome email
                    sendWelcomeEmail($email, $name);
                    
                    $_SESSION['success_message'] = "Registration successful! A welcome email has been sent to your address.";
                    header("Location: index.php");
                    exit();
                }
            }
        } catch (Exception $e) {
            $errors[] = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - StudentMarketHub</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/login.css">
    <style>
        .login-container {
            max-width: 500px;
        }
       
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
        .password-strength {
            height: 5px;
            background: #e9ecef;
            margin-top: 5px;
            border-radius: 3px;
            overflow: hidden;
        }
        .password-strength-bar {
            height: 100%;
            width: 0%;
            background: #28a745;
            transition: width 0.3s ease;
        }
        .success-message {
            color: #28a745;
            background-color: rgba(40, 167, 69, 0.1);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }
        .role-options {
            display: flex;
            gap: 15px;
            margin-top: 10px;
        }
        .role-option {
            flex: 1;
        }
        .role-option input[type="radio"] {
            display: none;
        }
        .role-option label {
            display: block;
            padding: 10px;
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .role-option input[type="radio"]:checked + label {
            border-color: #4a6bff;
            background: rgba(74, 107, 255, 0.1);
        }
    </style>
</head>
<body class="login-page">
    <div class="login-container">
        <div class="university-brand">
            <div class="university-name">ADAMA SCIENCE AND TECHNOLOGY UNIVERSITY</div>
            <div class="platform-name">Student MARKET HUB</div>
        </div>
        
        <h2>Create Your Account</h2>
        
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-message">
                <p><i class="fas fa-check-circle"></i> <?php echo $success; ?></p>
            </div>
        <?php else: ?>
            <form method="POST" action="" class="login-form" id="registrationForm">
                <div class="form-group">
                    <label for="name" class="required-field">Full Name</label>
                    <input type="text" id="name" name="name" required placeholder="Enter your full name" value="<?php echo htmlspecialchars($name ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="email" class="required-field">Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email" value="<?php echo htmlspecialchars($email ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" value="<?php echo htmlspecialchars($phone ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="password" class="required-field">Password</label>
                    <input type="password" id="password" name="password" required placeholder="At least 6 characters" oninput="updatePasswordStrength()">
                    <div class="password-strength">
                        <div class="password-strength-bar" id="passwordStrengthBar"></div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="required-field">Account Type</label>
                    <div class="role-options">
                        <div class="role-option">
                            <input type="radio" id="buyer" name="role" value="buyer" <?php echo ($role ?? '') === 'buyer' ? 'checked' : ''; ?> required>
                            <label for="buyer"><i class="fas fa-shopping-cart"></i> Buyer</label>
                        </div>
                        <div class="role-option">
                            <input type="radio" id="seller" name="role" value="seller" <?php echo ($role ?? '') === 'seller' ? 'checked' : ''; ?>>
                            <label for="seller"><i class="fas fa-store"></i> Seller</label>
                        </div>
                        <div class="role-option">
                            <input type="radio" id="both" name="role" value="both" <?php echo ($role ?? '') === 'both' ? 'checked' : ''; ?>>
                            <label for="both"><i class="fas fa-exchange-alt"></i> Both</label>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="login-btn">Register <i class="fas fa-user-plus"></i></button>
            </form>
        <?php endif; ?>
        
        <div class="login-links">
            <p>Already have an account? <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></p>
        </div>
    </div>

    <script>
        function updatePasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthBar = document.getElementById('passwordStrengthBar');
            let strength = 0;
            
            if (password.length >= 6) strength += 30;
            if (password.length >= 8) strength += 20;
            if (/[A-Z]/.test(password)) strength += 20;
            if (/[0-9]/.test(password)) strength += 20;
            if (/[^A-Za-z0-9]/.test(password)) strength += 10;
            
            strengthBar.style.width = Math.min(strength, 100) + '%';
            
            if (strength < 40) {
                strengthBar.style.background = '#dc3545';
            } else if (strength < 70) {
                strengthBar.style.background = '#ffc107';
            } else {
                strengthBar.style.background = '#28a745';
            }
        }
        
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            if (password.length < 6) {
                alert('Password must be at least 6 characters');
                e.preventDefault();
            }
        });
    </script>
</body>
</html>