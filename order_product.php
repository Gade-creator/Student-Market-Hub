<?php
include("includes/db_connect.php");
include("includes/functions.php");
session_start();

// Check login and role
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['buyer', 'both'])) {
    die('Access denied. Only buyers can place orders.');
}

// Get product ID
$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

// Fetch product
$product = null;
if ($product_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
}
if (!$product) {
    die('Product not found.');
}

$errors = [];
$success = '';
$pickup_location = '';
$phone = '';
$quantity = 1;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Initialize variables from POST data with proper checks
    $pickup_location = isset($_POST['pickup_location']) ? trim($_POST['pickup_location']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    // Validation
    if (empty($pickup_location)) {
        $errors[] = "Pickup location is required.";
    }
    
    if (empty($phone)) {
        $errors[] = "Phone number is required.";
    } elseif (!preg_match('/^[0-9]{10,15}$/', $phone)) {
        $errors[] = "Please enter a valid phone number (10-15 digits).";
    }
    
    if ($quantity < 1) {
        $errors[] = "Quantity must be at least 1.";
    }

    if (empty($errors)) {
        $buyer_id = $_SESSION['user_id'];
        $status = 'pending';
        $notified_seller = 0;
        $delivered_to_admin = 0;
        $completed = 0;
        $commission_amount = $product['price'] * $quantity * 0.1; // 10% commission

        try {
            // Insert order into database
            $stmt = $conn->prepare("INSERT INTO orders 
                                  (buyer_id, product_id, pickup_location, phone, status, 
                                   notified_seller, delivered_to_admin, completed, commission_amount) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iisssiiid", 
                            $buyer_id, $product_id, $pickup_location, $phone, $status,
                            $notified_seller, $delivered_to_admin, $completed, $commission_amount);
            $stmt->execute();

            // Get seller ID from product
            $seller_stmt = $conn->prepare("SELECT seller_id FROM products WHERE id = ?");
            $seller_stmt->bind_param("i", $product_id);
            $seller_stmt->execute();
            $seller_result = $seller_stmt->get_result();
            $seller = $seller_result->fetch_assoc();

            if ($seller) {
                // Notify seller
                $notification_message = "A new order has been placed for your product!";
                $notify_stmt = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
                $notify_stmt->bind_param("is", $seller['seller_id'], $notification_message);
                $notify_stmt->execute();
                
                // Update notified_seller status
                $order_id = $stmt->insert_id;
                $update_stmt = $conn->prepare("UPDATE orders SET notified_seller = 1 WHERE id = ?");
                $update_stmt->bind_param("i", $order_id);
                $update_stmt->execute();
            }

            // Notify admin
            $admin_id = 1; // assuming admin has user_id = 1
            $admin_message = "A new order has been placed by Buyer ID $buyer_id for Product ID $product_id";
            sendNotification($conn, $admin_id, $admin_message);

            $success = "Order placed successfully! The seller has been notified.";
            
            // Clear form fields after successful submission
            $pickup_location = '';
            $phone = '';
            $quantity = 1;
        } catch (Exception $e) {
            $errors[] = "Error processing order: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Product - StudentMarketHub</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/order_product.css">
</head>
<body class="order-page">
    <div class="order-container">
        <h2>Order Product</h2>

        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-message">
                <p><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?></p>
            </div>
        <?php endif; ?>

        <div class="product-details">
            <h3>Product Details</h3>
            <div class="product-info">
                <p><strong>Product Name:</strong> <?php echo htmlspecialchars($product['product_name']); ?></p>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($product['description']); ?></p>
                <p><strong>Price:</strong> <?php echo number_format($product['price'], 2); ?> ETB</p>
                <?php if (!empty($product['photo_path'])): ?>
                    <img src="<?php echo htmlspecialchars($product['photo_path']); ?>" alt="Product Image" class="product-image">
                <?php endif; ?>
            </div>
        </div>

        <div class="order-form">
            <h3>Order Information</h3>
            <form method="POST">
                <div class="form-group">
                    <label for="pickup_location">Pickup Location *</label>
                    <input type="text" id="pickup_location" name="pickup_location" required 
                           placeholder="Where will you pick up the product?"
                           value="<?php echo htmlspecialchars($pickup_location); ?>">
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number *</label>
                    <input type="tel" id="phone" name="phone" required 
                           placeholder="Your active phone number"
                           value="<?php echo htmlspecialchars($phone); ?>">
                </div>
                
                <div class="form-group">
                    <label for="quantity">Quantity *</label>
                    <input type="number" id="quantity" name="quantity" min="1" value="<?php echo $quantity; ?>" required>
                    <p class="hint">Total: <span id="total-price"><?php echo number_format($product['price'] * $quantity, 2); ?></span> ETB</p>
                </div>
                
                <button type="submit" class="order-btn">Place Order <i class="fas fa-shopping-cart"></i></button>
            </form>
        </div>
    </div>

    <script>
        // Calculate total price when quantity changes
        document.getElementById('quantity').addEventListener('input', function() {
            const quantity = this.value;
            const price = <?php echo $product['price']; ?>;
            document.getElementById('total-price').textContent = (quantity * price).toFixed(2);
        });
    </script>
</body>
</html>