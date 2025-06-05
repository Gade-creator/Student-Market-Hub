

<?php 
include('includes/db_connect.php');
include('includes/functions.php');

if (isset($_SESSION['user_id']) && ($_SESSION['user_role'] === 'buyer' || $_SESSION['user_role'] === 'both')): ?>
    <li><a href="my_orders.php">My Orders</a></li>
<?php endif; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=// Add this inside your <body> tag in header.php">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link src="css/style.css" ref="stylesheet">

<div class="notification-dropdown">
    <button class="notification-btn">Notifications</button>
    <div class="dropdown-content">
        <?php
        // Fetch notifications for the logged-in user
        $notifications = get_notifications($_SESSION['user_id']);
        if ($notifications->num_rows > 0):
            while ($notification = $notifications->fetch_assoc()):
        ?>
                <div class="notification">
                    <p><?php echo htmlspecialchars($notification['message']); ?></p>
                    <small><?php echo $notification['created_at']; ?></small>
                    <a href="mark_as_read.php?notification_id=<?php echo $notification['id']; ?>">Mark as Read</a>
                </div>
        <?php endwhile; ?>
        <?php else: ?>
            <p>No new notifications.</p>
        <?php endif; ?>
    </div>
</div>
, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
</body>
</html>