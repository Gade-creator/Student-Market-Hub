
<?php
// notifications.php - Display notifications for the logged-in user
session_start();
include('includes/db_connect.php');

$user_id = $_SESSION['user_id'];  // Assuming the user is logged in
$stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? AND status = 'unread' ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="notification-dropdown">
    <button class="notification-btn">Notifications</button>
    <div class="dropdown-content">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($notification = $result->fetch_assoc()): ?>
                <div class="notification">
                    <p><?php echo htmlspecialchars($notification['message']); ?></p>
                    <small><?php echo $notification['created_at']; ?></small>
                    <!-- Mark as read -->
                    <a href="mark_as_read.php?notification_id=<?php echo $notification['id']; ?>">Mark as Read</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No new notifications.</p>
        <?php endif; ?>
    </div>
</div>
