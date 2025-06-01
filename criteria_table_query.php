<?php
session_start();
require_once "connection.php";

// Check if user is logged in and has appropriate permissions
if (!isset($_SESSION['username']) || !in_array($_SESSION['userType'], ['Admin', 'Staff'])) {
    header("Location: login.php");
    exit();
}

// INSERT
if (isset($_POST['save_criteria'])) {
    $criteria_name = $conn->real_escape_string($_POST['criteria_name']);
    $fk_criteria_contest = $conn->real_escape_string($_POST['fk_criteria_contest']);
    $criteria_description = $conn->real_escape_string($_POST['criteria_description']);
    $max_score = $conn->real_escape_string($_POST['max_score']);

    try {
        $insert_query = "INSERT INTO criteria_table (criteria_name, fk_criteria_contest, criteria_description, max_score) 
                        VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("sisi", $criteria_name, $fk_criteria_contest, $criteria_description, $max_score);
        
        if ($stmt->execute()) {
            // Log the action
            if (isset($_SESSION['users_id'])) {
                $action = "Added new criteria '$criteria_name'";
                $log_query = "INSERT INTO logs_table (action, log_time, fk_logs_users) VALUES (?, NOW(), ?)";
                $stmt = $conn->prepare($log_query);
                $stmt->bind_param("si", $action, $_SESSION['users_id']);
                $stmt->execute();
            }

            $_SESSION['success'] = "Criteria added successfully";
        } else {
            throw new Exception("Error adding criteria");
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }

    // Redirect based on user type
    $redirect_page = ($_SESSION['userType'] === 'Admin') ? 'admin_dashboard.php' : 'organizer.php';
    header("Location: $redirect_page?page=criteria");
    exit();
}

// UPDATE
if (isset($_POST['update_criteria'])) {
    $criteria_id = $conn->real_escape_string($_POST['criteria_id']);
    $criteria_name = $conn->real_escape_string($_POST['criteria_name']);
    $fk_criteria_contest = $conn->real_escape_string($_POST['fk_criteria_contest']);
    $criteria_description = $conn->real_escape_string($_POST['criteria_description']);
    $max_score = $conn->real_escape_string($_POST['max_score']);

    try {
        $update_query = "UPDATE criteria_table 
                        SET criteria_name = ?, 
                            fk_criteria_contest = ?, 
                            criteria_description = ?, 
                            max_score = ? 
                        WHERE criteria_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("sisii", $criteria_name, $fk_criteria_contest, $criteria_description, $max_score, $criteria_id);
        
        if ($stmt->execute()) {
            // Log the update
            if (isset($_SESSION['users_id'])) {
                $action = "Updated criteria '$criteria_name'";
                $log_query = "INSERT INTO logs_table (action, log_time, fk_logs_users) VALUES (?, NOW(), ?)";
                $stmt = $conn->prepare($log_query);
                $stmt->bind_param("si", $action, $_SESSION['users_id']);
                $stmt->execute();
            }

            $_SESSION['success'] = "Criteria updated successfully";
        } else {
            throw new Exception("Error updating criteria");
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }

    // Redirect based on user type
    $redirect_page = ($_SESSION['userType'] === 'Admin') ? 'admin_dashboard.php' : 'organizer.php';
    header("Location: $redirect_page?page=criteria");
    exit();
}

// DELETE
if (isset($_GET['id'])) {
    $criteria_id = intval($_GET['id']);
    
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // First check if there are any scores using this criteria
        $check_query = "SELECT COUNT(*) as count FROM score_table WHERE fk_score_criteria = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("i", $criteria_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result['count'] > 0) {
            throw new Exception("Cannot delete criteria: There are scores associated with this criteria");
        }
        
        // Get criteria name for logging
        $name_query = "SELECT criteria_name FROM criteria_table WHERE criteria_id = ?";
        $stmt = $conn->prepare($name_query);
        $stmt->bind_param("i", $criteria_id);
        $stmt->execute();
        $criteria = $stmt->get_result()->fetch_assoc();
        
        // Delete the criteria
        $delete_query = "DELETE FROM criteria_table WHERE criteria_id = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("i", $criteria_id);
        
        if ($stmt->execute()) {
            // Log the deletion
            if (isset($_SESSION['users_id'])) {
                $action = "Deleted criteria '" . $criteria['criteria_name'] . "'";
                $log_query = "INSERT INTO logs_table (action, log_time, fk_logs_users) VALUES (?, NOW(), ?)";
                $stmt = $conn->prepare($log_query);
                $stmt->bind_param("si", $action, $_SESSION['users_id']);
                $stmt->execute();
            }
            
            $conn->commit();
            $_SESSION['success'] = "Criteria deleted successfully";
        } else {
            throw new Exception("Error deleting criteria");
        }
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }

    // Redirect based on user type
    $redirect_page = ($_SESSION['userType'] === 'Admin') ? 'admin_dashboard.php' : 'organizer.php';
    header("Location: $redirect_page?page=criteria");
    exit();
}

// If we get here, something went wrong
header("Location: " . ($_SESSION['userType'] === 'Admin' ? 'admin_dashboard.php' : 'organizer.php'));
exit();
?> 