<?php
include("../includes/db_connect.php");
include("../includes/functions.php");
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fetch user's products if seller
$products = [];
if (in_array($user['role'], ['seller', 'both'])) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE seller_id = ? ORDER BY created_at DESC LIMIT 3");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile - StudentMarketHub</title>
    <link rel="stylesheet" href="../css/profile.css">
    <?php include("../includes/header.php"); ?>
</head>
<body>
    <?php include("../includes/navbar.php"); ?>
    
    <div class="profile-container">
        <div class="profile-header">
            <div class="avatar-container">
                <img src="../uploads/avatars/<?php echo !empty($user['avatar']) ? $user['avatar'] : 'default.png'; ?>" 
                     alt="Profile Picture" class="profile-avatar">
                <a href="upload_avatar.php" class="avatar-edit-btn">
                    <i class="fas fa-camera"></i>
                </a>
            </div>
            
            <div class="profile-info">
                <h1><?php echo htmlspecialchars($user['name']); ?></h1>
                
                <div class="user-badge <?php echo $user['role']; ?>">
                    <i class="fas fa-<?php 
                        echo $user['role'] == 'buyer' ? 'shopping-cart' : 
                             ($user['role'] == 'seller' ? 'store' : 'exchange-alt'); 
                    ?>"></i>
                    <?php echo ucfirst($user['role']); ?>
                </div>
                
                <?php if (!empty($user['bio'])): ?>
                    <p class="user-bio"><?php echo nl2br(htmlspecialchars($user['bio'])); ?></p>
                <?php endif; ?>
                
                <div class="profile-stats">
                    <div class="stat">
                        <span class="stat-number"><?php echo count($products); ?></span>
                        <span class="stat-label">Products</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">0</span> <!-- Replace with actual orders -->
                        <span class="stat-label">Orders</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">4.8</span> <!-- Replace with actual rating -->
                        <span class="stat-label">Rating</span>
                    </div>
                </div>
            </div>
            
            <a href="edit_profile.php" class="edit-profile-btn">
                <i class="fas fa-edit"></i> Edit Profile
            </a>
        </div>
        
        <?php if (in_array($user['role'], ['seller', 'both'])): ?>
            <section class="profile-section">
                <h2><i class="fas fa-box-open"></i> My Products</h2>
                <div class="products-grid">
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <div class="product-card">
                                <img src="../<?php echo htmlspecialchars($product['photo_path']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                                <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                                <p class="price"><?php echo number_format($product['price'], 2); ?> ETB</p>
                                <a href="../product.php?id=<?php echo $product['id']; ?>" class="view-btn">View</a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-products">You haven't posted any products yet.</p>
                    <?php endif; ?>
                </div>
                <a href="../post_product.php" class="add-product-btn">
                    <i class="fas fa-plus"></i> Add New Product
                </a>
            </section>
        <?php endif; ?>
        
        <section class="profile-section">
            <h2><i class="fas fa-history"></i> Recent Activity</h2>
            <div class="activity-list">
                <!-- Activity items would go here -->
                <div class="activity-item">
                    <i class="fas fa-shopping-cart"></i>
                    <p>Purchased "Textbook" on May 15</p>
                </div>
            </div>
        </section>
    </div>
    
    <?php include("../includes/footer.php"); ?>
</body>
</html>