<?php
include("includes/db_connect.php");
include("includes/functions.php");
session_start();

// Ensure user is logged in and is a seller or has both roles
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'seller' && $_SESSION['user_role'] !== 'both')) {
    die('Access denied. Only sellers can deliver orders.');
}

$seller_id = $_SESSION['user_id'];
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id <= 0) {
    die("Invalid order ID.");
}

// Check if the seller is the owner of this order's product
$stmt = $conn->prepare("
    SELECT o.*, p.seller_id 
    FROM orders o
    JOIN products p ON o.product_id = p.id
    WHERE o.id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Order not found.");
}

$order = $result->fetch_assoc();

if ($order['seller_id'] != $seller_id) {
    die("You are not authorized to deliver this order.");
}

// Update order status to 'delivered_to_admin'
$update = $conn->prepare("UPDATE orders SET status = 'delivered_to_admin' WHERE id = ?");
$update->bind_param("i", $order_id);
$update->execute();

// Send notifications
$admin_id = 1; // Replace this with your actual admin user ID

$seller_message = "You have successfully delivered order #{$order_id} to the admin.";
$admin_message = "Seller has delivered order #{$order_id}. Please confirm the delivery.";

// Notify both users
sendNotification($conn, $seller_id, $seller_message);
sendNotification($conn, $admin_id, $admin_message);

// Show success
echo "<p style='color:green;'>Delivery successful. Notifications sent to admin and seller.</p>";
?>

<a href="seller_dashboard.php">← Back to Dashboard</a>
