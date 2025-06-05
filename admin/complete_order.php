<?php
include('../includes/db_connect.php');
include('../includes/functions.php');


// Ensure that the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /login.php?redirect=".urlencode($_SERVER['REQUEST_URI']));
    exit();
}

// Validate order ID
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    die('Invalid order ID.');
}

$order_id = (int)$_GET['order_id'];

// Fetch order details with prepared statement
$stmt = $conn->prepare("SELECT o.*, p.price, p.commission_rate 
                       FROM orders o
                       JOIN products p ON o.product_id = p.id
                       WHERE o.id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    die('Order not found.');
}

// Calculate commission
$commission_rate = $order['commission_rate'] / 100;
$commission = $order['price'] * $commission_rate;

// Update order status and commission
$update_stmt = $conn->prepare("UPDATE orders 
                              SET status = 'completed', 
                                  commission_amount = ?,
                                  completed_at = NOW()
                              WHERE id = ?");
$update_stmt->bind_param("di", $commission, $order_id);
$update_stmt->execute();

// Send notifications
$buyer_message = "Your order #{$order_id} has been completed. Please pick up your item.";
$seller_message = "Order #{$order_id} has been completed. Commission: {$commission} ETB.";

sendNotification($order['buyer_id'], $buyer_message);
sendNotification($order['seller_id'], $seller_message);

// Redirect with success message
$_SESSION['success_message'] = "Order #{$order_id} marked as completed successfully!";
header('Location: manage_orders.php');
exit();
?>