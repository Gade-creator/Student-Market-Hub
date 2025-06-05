<?php
include("../includes/db_connect.php");
include("../includes/functions.php");
session_start();

// Check admin authentication
if (!isset($_SESSION['user_id'])) {
    header("Location:admin_login.php");
    exit();
}

// Handle product deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $product_id = (int)$_GET['delete'];
    
    // Get product image path first
    $stmt = $conn->prepare("SELECT photo_path FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    
    if ($product) {
        // Delete the product image file if exists
        if (!empty($product['photo_path']) && file_exists("../".$product['photo_path'])) {
            unlink("../".$product['photo_path']);
        }
        
        // Delete the product from database
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        
        $_SESSION['success_message'] = "Product deleted successfully!";
        header("Location: manage_products.php");
        exit();
    }
}

// Fetch all products with seller information
$products = [];
$stmt = $conn->prepare("
    SELECT p.*, u.name as seller_name 
    FROM products p
    JOIN users u ON p.seller_id = u.id
    ORDER BY p.created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .products-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .page-title {
            font-size: 2rem;
            color: #343a40;
            margin: 0;
        }
        
        .add-product-btn {
            background-color: #4a6bff;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .add-product-btn:hover {
            background-color: #3a56d4;
            transform: translateY(-2px);
        }
        
        .products-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .products-table th, 
        .products-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .products-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #343a40;
        }
        
        .products-table tr:hover {
            background-color: #f5f7ff;
        }
        
        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        .action-btns {
            display: flex;
            gap: 10px;
        }
        
        .edit-btn, .delete-btn {
            padding: 8px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .edit-btn {
            background-color: #28a745;
            color: white;
        }
        
        .edit-btn:hover {
            background-color: #218838;
        }
        
        .delete-btn {
            background-color: #dc3545;
            color: white;
        }
        
        .delete-btn:hover {
            background-color: #c82333;
        }
        
        .no-products {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
            border-left: 4px solid #28a745;
        }
        
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border-left: 4px solid #dc3545;
        }
    </style>
</head>
<body>
    <?php include("admin_header.php"); ?>
    
    <div class="products-container">
        <div class="page-header">
            <h1 class="page-title">Manage Products</h1>
            <a href="add_product.php" class="add-product-btn">
                <i class="fas fa-plus"></i> Add New Product
            </a>
        </div>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($products)): ?>
            <table class="products-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Seller</th>
                        <th>Date Added</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td>
                                <?php if (!empty($product['photo_path'])): ?>
                                    <img src="../<?php echo htmlspecialchars($product['photo_path']); ?>" alt="Product Image" class="product-image">
                                <?php else: ?>
                                    <div class="no-image">No Image</div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                            <td><?php echo htmlspecialchars(substr($product['description'], 0, 50)); ?>...</td>
                            <td><?php echo number_format($product['price'], 2); ?> ETB</td>
                            <td><?php echo htmlspecialchars($product['seller_name']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($product['created_at'])); ?></td>
                            <td class="action-btns">
                                <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="edit-btn">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="manage_products.php?delete=<?php echo $product['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this product?');">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-products">
                <p><i class="fas fa-box-open fa-2x"></i></p>
                <h3>No Products Found</h3>
                <p>There are currently no products in the system.</p>
                <a href="add_product.php" class="add-product-btn">
                    <i class="fas fa-plus"></i> Add Your First Product
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include("admin_footer.php"); ?>
</body>
</html>