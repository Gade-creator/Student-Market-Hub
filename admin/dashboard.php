<?php
session_start();
// Ensure the user is logged in and is an admin

if (!isset($_SESSION['user_id'])) {
    header("Location:admin_login.php");
    exit;
}
// Include database connection and functions
include("../includes/db_connect.php");
include("../includes/functions.php");

include("mark_as_read.php");


// Get basic statistics
$totalOrders = $conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0];
$completedOrders = $conn->query("SELECT COUNT(*) FROM orders WHERE completed = 1")->fetch_row()[0];
$pendingDeliveries = $conn->query("SELECT COUNT(*) FROM orders WHERE delivered_to_admin = 0")->fetch_row()[0];
$totalUsers = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$totalSellers = $conn->query("SELECT COUNT(DISTINCT seller_id) FROM products")->fetch_row()[0];
$totalCommission = $conn->query("SELECT SUM(commission_amount) FROM orders WHERE completed = 1")->fetch_row()[0] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .dashboard-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }
        .card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 5px #ccc;
        }
        .card h3 {
            margin: 0;
            font-size: 18px;
            color: #555;
        }
        .card p {
            font-size: 24px;
            margin-top: 10px;
            font-weight: bold;
            color: #333;
        }
        .nav-links {
            margin-top: 30px;
        }
        .nav-links a {
            display: inline-block;
            margin-right: 20px;
            padding: 10px 15px;
            background-color: #0069d9;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
        }
        .nav-links a:hover {
            background-color: #0053ba;
        }
    </style>
</head>
<body>

<h2>Welcome, Admin 👋 <?php echo $_SESSION['username']?></h2>

<div class="dashboard-container">
    <div class="card">
        <h3>Total Orders</h3>
        <p><?= $totalOrders ?></p>
    </div>
    <div class="card">
        <h3>Completed Orders</h3>
        <p><?= $completedOrders ?></p>
    </div>
    <div class="card">
        <h3>Pending Deliveries</h3>
        <p><?= $pendingDeliveries ?></p>
    </div>
    <div class="card">
        <h3>Total Users</h3>
        <p><?= $totalUsers ?></p>
    </div>
    <div class="card">
        <h3>Total Sellers</h3>
        <p><?= $totalSellers ?></p>
    </div>
    <div class="card">
        <h3>Commission Earned</h3>
        <p>₦<?= number_format($totalCommission, 2) ?></p>
    </div>
</div>

<div class="nav-links">
    <a href="manage_orders.php">Manage Orders</a>
    <a href="manage_products.php">Manage Products</a>
    <a href="logout.php">Logout</a>
</div>

</body>
</html>
