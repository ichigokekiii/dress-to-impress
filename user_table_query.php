<?php
session_start();
require_once "connection.php";

// Check if user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['userType'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// CREATE
if (isset($_POST['save_user'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);
    $userType = $conn->real_escape_string($_POST['userType']);

    // Check if username already exists
    $check_query = "SELECT COUNT(*) as count FROM users_table WHERE username = '$username'";
    $result = $conn->query($check_query);
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        $_SESSION['error'] = "Username already exists!";
        header("Location: admin_dashboard.php?page=users");
        exit();
    }

    $query = "INSERT INTO users_table (username, password, userType) VALUES ('$username', '$password', '$userType')";
    
    if ($conn->query($query)) {
        // Get the current admin's user ID
        $admin_query = "SELECT users_id FROM users_table WHERE username = '" . $_SESSION['username'] . "'";
        $admin_result = $conn->query($admin_query);
        $admin_id = $admin_result->fetch_assoc()['users_id'];

        // Log the action
        $action = "Added new user: $username ($userType)";
        $log_query = "INSERT INTO logs_table (action, log_time, fk_logs_users) VALUES ('$action', NOW(), $admin_id)";
        $conn->query($log_query);

        $_SESSION['success'] = "User added successfully!";
    } else {
        $_SESSION['error'] = "Error adding user: " . $conn->error;
    }
    
    header("Location: admin_dashboard.php?page=users");
    exit();
}

// UPDATE
if (isset($_POST['update_user'])) {
    $user_id = intval($_POST['user_id']);
    $username = $conn->real_escape_string($_POST['username']);
    $userType = $conn->real_escape_string($_POST['userType']);
    $password = $_POST['password']; // Get password if provided

    // Check if username exists for other users
    $check_query = "SELECT COUNT(*) as count FROM users_table WHERE username = '$username' AND users_id != $user_id";
    $result = $conn->query($check_query);
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        $_SESSION['error'] = "Username already exists!";
        header("Location: admin_dashboard.php?page=users");
        exit();
    }

    // Build update query based on whether password was provided
    if (!empty($password)) {
        $password = $conn->real_escape_string($password);
        $query = "UPDATE users_table SET username = '$username', password = '$password', userType = '$userType' WHERE users_id = $user_id";
    } else {
        $query = "UPDATE users_table SET username = '$username', userType = '$userType' WHERE users_id = $user_id";
    }
    
    if ($conn->query($query)) {
        // Get the current admin's user ID
        $admin_query = "SELECT users_id FROM users_table WHERE username = '" . $_SESSION['username'] . "'";
        $admin_result = $conn->query($admin_query);
        $admin_id = $admin_result->fetch_assoc()['users_id'];

        // Log the action
        $action = "Updated user: $username ($userType)";
        $log_query = "INSERT INTO logs_table (action, log_time, fk_logs_users) VALUES ('$action', NOW(), $admin_id)";
        $conn->query($log_query);

        $_SESSION['success'] = "User updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating user: " . $conn->error;
    }
    
    header("Location: admin_dashboard.php?page=users");
    exit();
}

// DELETE
if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    
    // Prevent deleting the last admin user
    $admin_check_query = "SELECT COUNT(*) as admin_count FROM users_table WHERE userType = 'Admin'";
    $admin_result = $conn->query($admin_check_query);
    $admin_count = $admin_result->fetch_assoc()['admin_count'];
    
    $user_check_query = "SELECT userType FROM users_table WHERE users_id = $user_id";
    $user_result = $conn->query($user_check_query);
    $user_type = $user_result->fetch_assoc()['userType'];
    
    if ($admin_count <= 1 && $user_type === 'Admin') {
        $_SESSION['error'] = "Cannot delete the last admin user!";
        header("Location: admin_dashboard.php?page=users");
        exit();
    }

    // Check if user has related records in logs_table
    $check_logs = "SELECT COUNT(*) as log_count FROM logs_table WHERE fk_logs_users = $user_id";
    $logs_result = $conn->query($check_logs);
    $log_count = $logs_result->fetch_assoc()['log_count'];

    if ($log_count > 0) {
        $_SESSION['error'] = "Cannot delete user: There are logs associated with this user";
        header("Location: admin_dashboard.php?page=users");
        exit();
    }

    try {
        // Start transaction
        $conn->begin_transaction();

        // Delete the user
        $query = "DELETE FROM users_table WHERE users_id = $user_id";
        if ($conn->query($query)) {
            // Get the current admin's user ID for logging
            $admin_query = "SELECT users_id FROM users_table WHERE username = '" . $_SESSION['username'] . "'";
            $admin_result = $conn->query($admin_query);
            $admin_id = $admin_result->fetch_assoc()['users_id'];

            // Log the action
            $action = "Deleted user ID: $user_id";
            $log_query = "INSERT INTO logs_table (action, log_time, fk_logs_users) VALUES ('$action', NOW(), $admin_id)";
            $conn->query($log_query);

            // Get the maximum ID after deletion
            $max_id_query = "SELECT MAX(users_id) as max_id FROM users_table";
            $result = $conn->query($max_id_query);
            $max_id = ($result->fetch_assoc())['max_id'] ?? 0;
            
            // Reset auto-increment to max_id + 1
            $reset_auto_increment = "ALTER TABLE users_table AUTO_INCREMENT = " . ($max_id + 1);
            $conn->query($reset_auto_increment);

            // Commit transaction
            $conn->commit();
            
            $_SESSION['success'] = "User deleted successfully!";
        } else {
            throw new Exception($conn->error);
        }
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $_SESSION['error'] = "Error deleting user: " . $e->getMessage();
    }
    
    header("Location: admin_dashboard.php?page=users");
    exit();
}
?> 