<?php
include("includes/db_connect.php");
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'seller' && $_SESSION['user_role'] !== 'both') {
    die('Access denied. Only sellers can post products.');
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = trim($_POST['product_name']);
    $description  = trim($_POST['description']);
    $price        = $_POST['price'];
    $image        = $_FILES['image'];

    // Basic validation
    if (empty($product_name) || empty($description) || empty($price) || !$image['name']) {
        $errors[] = "All fields including image are required.";
    }

    // Handle file upload
    if (empty($errors)) {
        $target_dir = "uploads/";
        $image_name = basename($image['name']);
        $target_file = $target_dir . time() . "_" . $image_name;

        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($file_type, $allowed_types)) {
            $errors[] = "Only JPG, JPEG, PNG, and GIF files are allowed.";
        } elseif ($image['size'] > 2 * 1024 * 1024) {
            $errors[] = "Image must be less than 2MB.";
        } elseif (move_uploaded_file($image['tmp_name'], $target_file)) {
            // File uploaded, now insert product into DB
            $seller_id = $_SESSION['user_id'];

            $stmt = $conn->prepare("INSERT INTO products (product_name, description, price, image, seller_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdsi", $product_name, $description, $price, $target_file, $seller_id);
            $stmt->execute();

            $success = "Product posted successfully!";
        } else {
            $errors[] = "Failed to upload image.";
        }
    }
    if (!empty($errors)): ?>
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
    <?php endif; 
}
?>

<!-- Product Upload Form -->
<!DOCTYPE html>
<html>
<head>
    <title>Post Product</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/post_product.css">
</head>
<body>
    <h2>Post a New Product</h2>

    <?php if (!empty($errors)) foreach ($errors as $error) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Product Name:</label><br>
        <input type="text" name="product_name" required><br><br>

        <label>Description:</label><br>
        <textarea name="description" required></textarea><br><br>

        <label>Price (in ETB):</label><br>
        <input type="number" name="price" step="0.01" required><br><br>

        <label>Product Image:</label><br>
        <input type="file" name="image" accept="image/*" required><br><br>

        <button type="submit">Post Product</button>
    </form>
</body>
</html>
