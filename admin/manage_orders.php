<?php
include("../includes/db_connect.php");
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location:admin_login.php");
    exit;
}

// Optional: Protect with admin login
// if ($_SESSION['user_role'] !== 'admin') die("Access denied.");

// Handle marking as delivered
if (isset($_GET['order_id']) && isset($_GET['action']) && $_GET['action'] == 'delivered') {
    $order_id = $_GET['order_id'];

    // Update the order status to 'delivered_to_admin'
    $stmt = $conn->prepare("UPDATE orders SET delivered_to_admin = 1 WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();

    // Send notification to the admin
    $admin_id = 1; // Example admin ID, replace as needed
    $notification_message = "Order #{$order_id} has been marked as delivered by the seller.";

    // Insert notification into the database
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $stmt->bind_param("is", $admin_id, $notification_message);
    $stmt->execute();

    // Redirect to avoid resubmission of the form
    header("Location: manage_orders.php");
    exit;
}

// Handle completing the order
if (isset($_GET['order_id']) && isset($_GET['action']) && $_GET['action'] == 'complete') {
    $order_id = $_GET['order_id'];

    // Update the order status to 'completed'
    $stmt = $conn->prepare("UPDATE orders SET completed = 1 WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();

    // Redirect to avoid resubmission of the form
    header("Location: manage_orders.php");
    exit;
}

// Fetch all orders
$stmt = $conn->prepare("
    SELECT 
        o.id AS order_id,
        o.status,
        o.pickup_location,
        o.phone,
        o.notified_seller,
        o.delivered_to_admin,
        o.completed,
        o.created_at,
        p.product_name,
        s.name AS seller_name,
        b.name AS buyer_name
    FROM orders o
    JOIN products p ON o.product_id = p.id
    JOIN users s ON p.seller_id = s.id
    JOIN users b ON o.buyer_id = b.id
    ORDER BY o.created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Orders</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h2>All Orders</h2>

    <?php if ($result->num_rows > 0): ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <tr>
                <th>Order ID</th>
                <th>Product</th>
                <th>Buyer</th>
                <th>Seller</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Pickup Location</th>
                <th>Created At</th>
                <th>Notify Seller</th>
                <th>Mark Delivered</th>
                <th>Complete Order</th>
            </tr>
            <?php while ($order = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $order['order_id'] ?></td>
                    <td><?= htmlspecialchars($order['product_name']) ?></td>
                    <td><?= htmlspecialchars($order['buyer_name']) ?></td>
                    <td><?= htmlspecialchars($order['seller_name']) ?></td>
                    <td><?= htmlspecialchars($order['phone']) ?></td>
                    <td><?= ucfirst($order['status']) ?></td>
                    <td><?= htmlspecialchars($order['pickup_location']) ?></td>
                    <td><?= $order['created_at'] ?></td>

                    <td>
                        <?php if (!$order['notified_seller']): ?>
                            <a href="notify_seller.php?order_id=<?= $order['order_id'] ?>">Notify</a>
                        <?php else: ?>
                            ✅ Notified
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php if (!$order['delivered_to_admin']): ?>
                            <a href="manage_orders.php?order_id=<?= $order['order_id'] ?>&action=delivered">Mark as Delivered</a>
                        <?php else: ?>
                            ✅ Delivered
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php if (!$order['completed']): ?>
                            <a href="manage_orders.php?order_id=<?= $order['order_id'] ?>&action=complete">Complete Order</a>
                        <?php else: ?>
                            ✅ Completed
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No orders found.</p>
    <?php endif; ?>
</body>
</html>
