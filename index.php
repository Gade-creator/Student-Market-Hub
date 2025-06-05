<?php

include("includes/db_connect.php");
session_start();

// Fetch featured products
$stmt = $conn->prepare("SELECT * FROM products ORDER BY created_at DESC LIMIT 6");
$stmt->execute();
$featured_products = $stmt->get_result();

// Search functionality
$search_results = null;
if (isset($_GET['query'])) {
    $search_query = "%" . $_GET['query'] . "%";
    $search_stmt = $conn->prepare("SELECT * FROM products WHERE product_name LIKE ? OR description LIKE ?");
    $search_stmt->bind_param("ss", $search_query, $search_query);
    $search_stmt->execute();
    $search_results = $search_stmt->get_result();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentMarketHub - Buy & Sell Campus Products</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="uploads/logo-favicon.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
</head>
<body>

<!-- Header Section -->
<!-- Header Section -->
<header class="main-header">
    
    <div class="container">
        <nav class="navbar">
            <div class="logo">
                <a href="index.php" class="logo-link">
                    <div class="logo-text">
                        <span class="logo-line-1">Student</span>
                        <span class="logo-line-2">MARKET</span>
                        <span class="logo-line-3">HUB</span>
                    </div>
                    <div class="logo-badge">ASTU</div>
                </a>
            </div>
            <ul class="nav-links">
                <li><a href="index.php" class="active"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="view_products.php"><i class="fas fa-store"></i> Products</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    
                    <li><a href="post_product.php"><i class="fas fa-plus-circle"></i> Sell</a></li>
                    <li><a href="my_orders.php"><i class="fas fa-shopping-bag"></i> My Orders</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                   
                <?php else: ?>
                    <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                    <li><a href="register.php"><i class="fas fa-user-plus"></i> Register</a></li>
                <?php endif; ?>
            </ul>
            <div class="hamburger">
                <i class="fas fa-bars"></i>
            </div>
        </nav>
    </div>
</header>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1>Buy & Sell Campus Products Easily</h1>
            <p>Connect with fellow students to trade textbooks, electronics, clothes and more - all in one safe platform</p>
            <div class="hero-buttons">
                <a href="view_products.php" class="btn btn-primary">Browse Products</a>
                <?php if(!isset($_SESSION['user_id'])): ?>
                    <a href="register.php" class="btn btn-secondary">Join Now - It's Free</a>
                <?php else: ?>
                    <a href="post_product.php" class="btn btn-secondary">Sell Your Items</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Search Section -->
<section class="search-section">
    <div class="container">
        <form action="index.php" method="GET" class="search-form">
            <div class="search-box">
                <input type="text" name="query" placeholder="Search for products (books, electronics, clothes...)" required>
                <button type="submit"><i class="fas fa-search"></i> Search</button>
            </div>
        </form>
    </div>
</section>

<!-- Search Results (if any) -->
<?php if(isset($_GET['query']) && $search_results->num_rows > 0): ?>
    <section class="products-section">
        <div class="container">
            <h2 class="section-title">Search Results for "<?php echo htmlspecialchars($_GET['query']); ?>"</h2>
            <div class="products-grid">
                <?php while($product = $search_results->fetch_assoc()): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                        </div>
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                            <p class="price"><?php echo htmlspecialchars($product['price']); ?> ETB</p>
                            <p class="description"><?php echo substr(htmlspecialchars($product['description']), 0, 100); ?>...</p>
                            <a href="order_product.php?id=<?php echo $product['id']; ?>" class="btn btn-small">Buy Now</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
<?php elseif(isset($_GET['query'])): ?>
    <section class="no-results">
        <div class="container">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <p>No products found for "<?php echo htmlspecialchars($_GET['query']); ?>". Try a different search term or <a href="post_product.php">post this product</a> if you're selling it.</p>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Featured Products Section -->
<section class="products-section">
    <div class="container">
        <h2 class="section-title">Featured Products</h2>
        <div class="products-grid">
            <?php if($featured_products->num_rows > 0): ?>
                <?php while($product = $featured_products->fetch_assoc()): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['pruduct_name']); ?>">
                        </div>
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                            <p class="price"><?php echo htmlspecialchars($product['price']); ?> ETB</p>
                            <p class="description"><?php echo substr(htmlspecialchars($product['description']), 0, 100); ?>...</p>
                            <a href="order_product.php?product_id=<?php echo $product['id']; ?>" class="btn btn-small">Buy Now</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-products">
                    <p>No featured products available at the moment. Check back later!</p>
                </div>
            <?php endif; ?>
        </div>
        <div class="view-all">
            <a href="view_products.php" class="btn btn-outline">View All Products</a>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="how-it-works">
    <div class="container">
        <h2 class="section-title">How StudentMarketHub Works</h2>
        <div class="steps">
            <div class="step">
                <div class="step-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h3>1. Register</h3>
                <p>Create your free account as a buyer or seller</p>
            </div>
            <div class="step">
                <div class="step-icon">
                    <i class="fas fa-store"></i>
                </div>
                <h3>2. Browse or List</h3>
                <p>Find what you need or post items to sell</p>
            </div>
            <div class="step">
                <div class="step-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h3>3. Order Safely</h3>
                <p>Place orders through our secure system</p>
            </div>
            <div class="step">
                <div class="step-icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <h3>4. Meet & Complete</h3>
                <p>Admin facilitates safe product exchange</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials">
    <div class="container">
        <h2 class="section-title">What Our Users Say</h2>
        <div class="testimonial-grid">
            <div class="testimonial-card">
                <div class="quote">
                    <i class="fas fa-quote-left"></i>
                    <p>I sold my textbooks easily after exams and made back most of what I paid. Highly recommend!</p>
                </div>
                <div class="author">
                    <div class="author-info">
                        <h4>Samuel T.</h4>
                        <p>Computer Science Student</p>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="quote">
                    <i class="fas fa-quote-left"></i>
                    <p>Found affordable engineering books that weren't available in the library. Saved me so much money!</p>
                </div>
                <div class="author">
                    <div class="author-info">
                        <h4>Meron A.</h4>
                        <p>Engineering Student</p>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="quote">
                    <i class="fas fa-quote-left"></i>
                    <p>As someone from a coffee-growing region, this platform helped me share my family's products with campus.</p>
                </div>
                <div class="author">
                    <div class="author-info">
                        <h4>Kebede M.</h4>
                        <p>Sofftware Engineering Student</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>Ready to Start Buying or Selling?</h2>
            <p>Join hundreds of ASTU students already using StudentMarketHub</p>
            <div class="cta-buttons">
                <?php if(!isset($_SESSION['user_id'])): ?>
                    <a href="register.php" class="btn btn-primary">Sign Up Free</a>
                    <a href="login.php" class="btn btn-outline">Login</a>
                <?php else: ?>
                    <a href="post_product.php" class="btn btn-primary">Sell an Item</a>
                    <a href="view_products.php" class="btn btn-outline">Browse Products</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Footer Section -->
<footer class="main-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <h3>StudentMarketHub</h3>
                <p>Connecting ASTU students to buy and sell products safely and conveniently.</p>
                <div class="social-links">
                    <a href="https://www.facebook.com/accounts/login/"><i class="fab fa-facebook"></i></a>
                    <a href="https://web.telegram.org/k/"><i class="fab fa-telegram"></i></a>
                    <a href="https://www.instagram.com/accounts/login/"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="footer-col">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="view_products.php">Products</a></li>
                    <li><a href="post_product.php">Sell</a></li>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>Help</h3>
                <ul>
                    <li><a href="#">How It Works</a></li>
                    <li><a href="help/faqs.php">FAQs</a></li>
                    <li><a href="help/contact-admin.php">Contact Admin</a></li>
                    <li><a href="help/privacy-policy.php">Privacy Policy</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>Contact</h3>
                <ul>
                    <li><i class="fas fa-map-marker-alt"></i> ASTU Main Campus</li>
                    <li><i class="fas fa-phone"></i> +251 94635037</li>
                    <li><i class="fas fa-envelope"></i>info@studentmarkethub.com</li>
                    <li><i class=""></i></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 StudentMarketHub. All Rights Reserved.</p>
        </div>
    </div>
</footer>
<!-- JavaScript -->
<script src="js/main.js"></script>
</body>
</html>