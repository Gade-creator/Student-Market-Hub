<?php
// functions.php

if (!function_exists('sendNotification')) {
    function sendNotification($user_id, $message) {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $message);
        return $stmt->execute();
    }
}

if (!function_exists('insert_notification')) {
    function insert_notification($user_id, $message) {
        // This is redundant since it does the same as sendNotification
        // Consider removing this function and using sendNotification instead
        global $conn;
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $message);
        return $stmt->execute();
    }
}

if (!function_exists('get_notifications')) {
    function get_notifications($user_id) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? AND status = 'unread' ORDER BY created_at DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>