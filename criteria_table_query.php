<?php
session_start();
require_once "connection.php";

if (!isset($_SESSION['username']) || !in_array($_SESSION['userType'], ['Admin', 'Staff'])) {
    header("Location: login.php");
    exit();
}


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

    $redirect_page = ($_SESSION['userType'] === 'Admin') ? 'admin_dashboard.php' : 'organizer.php';
    header("Location: $redirect_page?page=criteria");
    exit();
}


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
    
    // Check for related records in score_table
    $check_query = "SELECT COUNT(*) as count FROM score_table WHERE fk_score_criteria = $criteria_id";
    $check_result = $conn->query($check_query);
    $row = $check_result->fetch_assoc();
    
    if ($row['count'] > 0) {
        $_SESSION['error'] = "Cannot delete criteria: There are scores associated with this criteria";
        header("Location: admin_dashboard.php?page=criteria");
        exit();
    }

    try {
        // Start transaction
        $conn->begin_transaction();

        // Delete the criteria
        $query = "DELETE FROM criteria_table WHERE criteria_id = $criteria_id";
        if ($conn->query($query)) {
            // Get the maximum ID after deletion
            $max_id_query = "SELECT MAX(criteria_id) as max_id FROM criteria_table";
            $result = $conn->query($max_id_query);
            $max_id = ($result->fetch_assoc())['max_id'] ?? 0;
            
            // Reset auto-increment to max_id + 1
            $reset_auto_increment = "ALTER TABLE criteria_table AUTO_INCREMENT = " . ($max_id + 1);
            $conn->query($reset_auto_increment);

            // Commit transaction
            $conn->commit();
            
            $_SESSION['success'] = "Criteria deleted successfully!";
        } else {
            throw new Exception($conn->error);
        }
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $_SESSION['error'] = "Error deleting criteria: " . $e->getMessage();
    }
    
    header("Location: admin_dashboard.php?page=criteria");
    exit();
}

// If we get here, something went wrong
header("Location: " . ($_SESSION['userType'] === 'Admin' ? 'admin_dashboard.php' : 'organizer.php'));
exit();
?> 