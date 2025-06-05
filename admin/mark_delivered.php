<?php
include('../includes/db_connect.php');
include('../includes/functions.php');
session_start();

// Check if order_id is provided (via GET request, as used in manage_orders.php)
if (!isset($_GET['order_id'])) {
    die("Order ID missing.");
}

$order_id = intval($_GET['order_id']);

// Mark order as delivered to admin
$stmt = $conn->prepare("UPDATE orders SET delivered_to_admin = 1, status = 'delivered' WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();

// Send notification to admin
$admin_id = 1; // Replace this with the actual admin ID from your DB if dynamic
$message = "Order #$order_id has been marked as delivered by the seller.";
sendNotification($conn, $admin_id, $message);

// Redirect back
header("Location: manage_orders.php");
exit;
?>
