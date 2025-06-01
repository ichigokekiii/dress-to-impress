<?php
session_start();
require_once "connection.php";

// Debug information
error_log("Session data: " . print_r($_SESSION, true));
error_log("POST data: " . print_r($_POST, true));

// Check if user is authorized (either Admin or Staff)
if (!isset($_SESSION['username']) || !in_array($_SESSION['userType'], ['Admin', 'Staff'])) {
    error_log("Authorization failed: userType=" . ($_SESSION['userType'] ?? 'not set'));
    $response = array(
        'status' => 'error',
        'message' => 'Unauthorized access. User type: ' . ($_SESSION['userType'] ?? 'not set')
    );
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Handle Contest Operations
if (isset($_POST['save_contest'])) {
    $contest_name = $conn->real_escape_string($_POST['contest_name']);
    $contest_date = $conn->real_escape_string($_POST['contest_date']);
    $location = $conn->real_escape_string($_POST['location']);

    // Find the lowest available ID
    $result = $conn->query("SELECT MIN(t1.contest_id + 1) AS next_id
                           FROM contest_table t1
                           LEFT JOIN contest_table t2 ON t1.contest_id + 1 = t2.contest_id
                           WHERE t2.contest_id IS NULL");
    $row = $result->fetch_assoc();
    $next_id = $row['next_id'] ?? 1;

    $query = "INSERT INTO contest_table (contest_id, contest_name, contest_date, location) 
              VALUES ($next_id, '$contest_name', '$contest_date', '$location')";

    error_log("Executing query: " . $query);

    $response = array();
    if ($conn->query($query)) {
        // Log the action
        $username = $_SESSION['username'];
        $action = "Added new contest: $contest_name";
        $log_query = "INSERT INTO logs_table (action, log_time) 
                     VALUES ('$action', NOW())";
        $conn->query($log_query);

        $response['status'] = 'success';
        $response['message'] = 'Contest added successfully';
    } else {
        error_log("Database error: " . $conn->error);
        $response['status'] = 'error';
        $response['message'] = 'Error adding contest: ' . $conn->error;
    }
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

if (isset($_POST['update_contest'])) {
    $contest_id = intval($_POST['contest_id']);
    $contest_name = $conn->real_escape_string($_POST['contest_name']);
    $contest_date = $conn->real_escape_string($_POST['contest_date']);
    $location = $conn->real_escape_string($_POST['location']);

    $query = "UPDATE contest_table 
              SET contest_name = '$contest_name', 
                  contest_date = '$contest_date', 
                  location = '$location'
              WHERE contest_id = $contest_id";

    $response = array();
    if ($conn->query($query)) {
        // Log the action
        $username = $_SESSION['username'];
        $action = "Updated contest ID $contest_id: $contest_name";
        $log_query = "INSERT INTO logs_table (action, log_time) 
                     VALUES ('$action', NOW())";
        $conn->query($log_query);

        $response['status'] = 'success';
        $response['message'] = 'Contest updated successfully';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Error updating contest: ' . $conn->error;
    }
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

if (isset($_GET['delete_contest'])) {
    $contest_id = intval($_GET['delete_contest']);
    
    // Check for related records
    $check_query = "SELECT COUNT(*) as count FROM contestant_table WHERE fk_contestant_contest = $contest_id";
    $check_result = $conn->query($check_query);
    $row = $check_result->fetch_assoc();
    
    $response = array();
    if ($row['count'] > 0) {
        $response['status'] = 'error';
        $response['message'] = 'Cannot delete contest: There are contestants associated with this contest';
    } else {
        $delete_query = "DELETE FROM contest_table WHERE contest_id = $contest_id";
        if ($conn->query($delete_query)) {
            // Log the action
            $username = $_SESSION['username'];
            $action = "Deleted contest ID $contest_id";
            $log_query = "INSERT INTO logs_table (action, log_time) 
                         VALUES ('$action', NOW())";
            $conn->query($log_query);

            // Reset auto-increment to the next available ID
            $max_query = "SELECT MAX(contest_id) as max_id FROM contest_table";
            $max_result = $conn->query($max_query);
            $max_row = $max_result->fetch_assoc();
            $next_id = ($max_row['max_id'] ?? 0) + 1;
            
            // Reset the auto-increment
            $conn->query("ALTER TABLE contest_table AUTO_INCREMENT = $next_id");

            $response['status'] = 'success';
            $response['message'] = 'Contest deleted successfully';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Error deleting contest: ' . $conn->error;
        }
    }
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?> 