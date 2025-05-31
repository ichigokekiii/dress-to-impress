<?php
session_start();
require_once "connection.php";

// Check if user is logged in and is a staff
if (!isset($_SESSION['user_id']) || $_SESSION['userType'] !== 'Staff') {
    header("Location: login.php");
    exit();
}

// Add Contest
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

// Update Contest
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

// Delete Contest
if (isset($_GET['id'])) {
    $contest_id = intval($_GET['id']);

    // Check for related records
    $check_query = "SELECT COUNT(*) as count FROM contestant_table WHERE fk_contestant_contest = $contest_id";
    $check_result = $conn->query($check_query);
    $row = $check_result->fetch_assoc();

    if ($row['count'] > 0) {
        $_SESSION['message'] = "Cannot delete contest: There are contestants associated with this contest";
    } else {
        $delete_query = "DELETE FROM contest_table WHERE contest_id = $contest_id";
        if ($conn->query($delete_query)) {
            $_SESSION['message'] = "Contest deleted successfully";
        } else {
            $_SESSION['message'] = "Error deleting contest: " . $conn->error;
        }
    }

    header("Location: organizer_dashboard.php?page=contests");
    exit();
}
?>
