<?php
include("includes/db_connect.php");
session_start();

// Ensure the user is logged in and is either a buyer or has both roles
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'buyer' && $_SESSION['user_role'] !== 'both')) {
    die('Access denied. Only buyers can view their orders.');
    
}

$buyer_id = $_SESSION['user_id'];
// Fetch orders made by the current user (buyer)
$stmt = $conn->prepare("
    SELECT 
        o.id AS order_id,
        p.product_name,
        o.pickup_location,
        o.phone,
        o.status,
        o.created_at,
        o.completed,
        o.delivered_to_admin,
        o.notified_seller
    FROM 
        orders o
    JOIN 
        products p ON o.product_id = p.id
    WHERE 
        o.buyer_id = ?
    ORDER BY 
        o.created_at DESC
");
$stmt->bind_param("i", $buyer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Orders</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h2>My Orders</h2>

    <?php if ($result->num_rows > 0): ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <tr>
                <th>Order ID</th>
                <th>Product Name</th>
                <th>Pickup Location</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Ordered At</th>
            </tr>
            <?php while ($order = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $order['order_id']; ?></td>
                    <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($order['pickup_location']); ?></td>
                    <td><?php echo htmlspecialchars($order['phone']); ?></td>
                    <td><?php echo htmlspecialchars(ucfirst($order['status'])); ?></td>
                    <td><?php echo $order['created_at']; ?></td>
                </tr>
                
                <td>
                    <?php
                        // Check the status of the order and display appropriate message
                        if (!empty($order['completed'])) {
                            echo "Completed";
                        }elseif(!empty($order['delivered_to_admin'])) {
                            echo "Delivered to Admin, Awaiting Pickup";
                        }elseif (empty($order['notified_seller'])) {
                            echo "Seller Notified";                       
                        }else {
                            echo "Pending"; 
                        }


                    ?>
                </td> 
            <?php endwhile; ?> 

        </table>
    <?php else: ?>
        <p>You haven't placed any orders yet.</p>
    <?php endif; ?>

</body>
</html>


