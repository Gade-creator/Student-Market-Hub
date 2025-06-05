<?php
include("../includes/db_connect.php");
include("../includes/functions.php");
session_start();

if (!isset($_GET['order_id'])) {
    die("Missing order ID.");
}

$order_id = intval($_GET['order_id']);

// Fetch the seller's ID based on the order
$stmt = $conn->prepare("SELECT p.seller_id FROM orders o JOIN products p ON o.product_id = p.id WHERE o.id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Order not found.");
}

$order = $result->fetch_assoc();
$seller_id = $order['seller_id'];

// Update the order to mark seller as notified
$stmt = $conn->prepare("UPDATE orders SET notified_seller = 1 WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();

// After setting 'notified_seller' = 1, send notification to the seller
$message = "You have been notified to deliver a product. Order ID: $order_id.";
sendNotification($conn, $seller_id, $message);

header("Location: manage_orders.php");
exit;
?>
