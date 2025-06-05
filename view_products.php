<?php
include("includes/db_connect.php");
session_start();

// Fetch products from database
$sql = "SELECT * FROM products ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Products</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/view_product.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .product-card {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 15px;
            margin: 10px;
            width: 250px;
            display: inline-block;
            vertical-align: top;
            text-align: center;
        }
        .product-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h2>Available Products</h2>

    <?php
    if ($result && $result->num_rows > 0):
        while ($row = $result->fetch_assoc()):
    ?>
        <div class="product-card">
            <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Product Image">
            <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>
            <p><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
            <p><strong>Price:</strong> <?php echo number_format($row['price'], 2); ?> ETB</p>
            <a href="order_product.php?product_id=<?php echo $row['id']; ?>">Order Now</a>
        </div>
    <?php
        endwhile;
    else:
        echo "<p>No products found.</p>";
    endif;
    ?>

</body>
</html>
