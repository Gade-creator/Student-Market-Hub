
<?php
// mark_as_read.php - Mark the notification as read

include("../includes/db_connect.php");


// Check if notification ID is passed
if (isset($_GET['notification_id'])) {
    $notification_id = $_GET['notification_id'];

    // Update notification status to "read"
    $stmt = $conn->prepare("UPDATE notifications SET status = 'read' WHERE id = ?");
    $stmt->bind_param("i", $notification_id);
    $stmt->execute();

    // Redirect back to the page where notifications are displayed
    header("Location: notifications.php");
}
?>