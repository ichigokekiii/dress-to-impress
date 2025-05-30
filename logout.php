<?php
session_start();
require_once "connection.php";

// Log the logout action if user is logged in
if (isset($_SESSION['user_id'])) {
    try {
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("INSERT INTO logs_table (user_id, action, log_time) VALUES (?, 'User logged out', NOW())");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    } catch (Exception $e) {
        // Continue with logout even if logging fails
        error_log("Failed to log logout action: " . $e->getMessage());
    }
}

// Destroy all session data
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
?> 