<?php
session_start();
require_once "connection.php";


if (!isset($_SESSION['user_id']) || $_SESSION['userType'] !== 'Staff') {
    header("Location: login.php");
    exit();
}


if (isset($_POST['save_contest'])) {
    $contest_name = $conn->real_escape_string($_POST['contest_name']);
    $contest_date = $conn->real_escape_string($_POST['contest_date']);
    $location = $conn->real_escape_string($_POST['location']);

    $query = "INSERT INTO contest_table (contest_name, contest_date, location) 
              VALUES ('$contest_name', '$contest_date', '$location')";

    if ($conn->query($query)) {
        $_SESSION['message'] = "Contest added successfully";
    } else {
        $_SESSION['message'] = "Error adding contest: " . $conn->error;
    }

    header("Location: organizer_dashboard.php?page=contests");
    exit();
}


if (isset($_POST['update_contest'])) {
    $contest_id = intval($_POST['contest_id']);
    $contest_name = $conn->real_escape_string($_POST['contest_name']);
    $contest_date = $conn->real_escape_string($_POST['contest_date']);
    $location = $conn->real_escape_string($_POST['location']);

    $query = "UPDATE contest_table 
              SET contest_name = '$contest_name', contest_date = '$contest_date', location = '$location'
              WHERE contest_id = $contest_id";

    if ($conn->query($query)) {
        $_SESSION['message'] = "Contest updated successfully";
    } else {
        $_SESSION['message'] = "Error updating contest: " . $conn->error;
    }

    header("Location: organizer_dashboard.php?page=contests");
    exit();
}


if (isset($_GET['id'])) {
    $contest_id = intval($_GET['id']);

    try {
        // Start transaction
        $conn->begin_transaction();

        // Delete the contest
        $query = "DELETE FROM contest_table WHERE contest_id = $contest_id";
        if ($conn->query($query)) {
            // Get the maximum ID after deletion
            $max_id_query = "SELECT MAX(contest_id) as max_id FROM contest_table";
            $result = $conn->query($max_id_query);
            $max_id = ($result->fetch_assoc())['max_id'] ?? 0;
            
            // Reset auto-increment to max_id + 1
            $reset_auto_increment = "ALTER TABLE contest_table AUTO_INCREMENT = " . ($max_id + 1);
            $conn->query($reset_auto_increment);

            // Commit transaction
            $conn->commit();
            
            $_SESSION['success'] = "Contest deleted successfully!";
        } else {
            throw new Exception($conn->error);
        }
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $_SESSION['error'] = "Error deleting contest: " . $e->getMessage();
    }
    
    header("Location: admin_dashboard.php?page=contests");
    exit();
}
?>
