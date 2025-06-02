<?php
session_start();
require_once "connection.php";

if (!isset($_SESSION['username']) || !in_array($_SESSION['userType'], ['Admin', 'Staff'])) {
    header("Location: login.php");
    exit();
}

// Add Score
if (isset($_POST['save_score'])) {
    $judge_id = $conn->real_escape_string($_POST['judge_id']);
    $contestant_id = $conn->real_escape_string($_POST['contestant_id']);
    $criteria_id = $conn->real_escape_string($_POST['criteria_id']);
    $score_value = $conn->real_escape_string($_POST['score_value']);
    $remarks = $conn->real_escape_string($_POST['remarks'] ?? '');

    try {
        // Start transaction
        $conn->begin_transaction();

        // Check if score already exists
        $check_query = "SELECT score_id FROM score_table 
                       WHERE fk_score_judge = ? AND fk_score_contestant = ? AND fk_score_criteria = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("iii", $judge_id, $contestant_id, $criteria_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['error'] = "Score already exists for this judge, contestant, and criteria combination.";
            $conn->rollback();
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

        // Get max score for the criteria
        $max_score_query = "SELECT max_score FROM criteria_table WHERE criteria_id = ?";
        $max_score_stmt = $conn->prepare($max_score_query);
        $max_score_stmt->bind_param("i", $criteria_id);
        $max_score_stmt->execute();
        $max_score_result = $max_score_stmt->get_result();
        $max_score = $max_score_result->fetch_assoc()['max_score'];

        // Validate score value
        if ($score_value < 0 || $score_value > $max_score) {
            $_SESSION['error'] = "Score must be between 0 and " . $max_score;
            $conn->rollback();
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

        // Insert score
        $query = "INSERT INTO score_table (fk_score_judge, fk_score_contestant, fk_score_criteria, score_value, remarks) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiids", $judge_id, $contestant_id, $criteria_id, $score_value, $remarks);
        $stmt->execute();

        // Log the action
        $log_query = "INSERT INTO activity_log (username, action, details) VALUES (?, 'Add Score', ?)";
        $log_stmt = $conn->prepare($log_query);
        $details = "Added score: Judge ID: $judge_id, Contestant ID: $contestant_id, Criteria ID: $criteria_id, Score: $score_value";
        $log_stmt->bind_param("ss", $_SESSION['username'], $details);
        $log_stmt->execute();

        $conn->commit();
        $_SESSION['message'] = "Score added successfully";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Error adding score: " . $e->getMessage();
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// Update Score
if (isset($_POST['update_score'])) {
    $score_id = $conn->real_escape_string($_POST['score_id']);
    $score_value = $conn->real_escape_string($_POST['score_value']);
    $remarks = $conn->real_escape_string($_POST['remarks'] ?? '');

    try {
        // Start transaction
        $conn->begin_transaction();

        // Get criteria_id and max_score
        $criteria_query = "SELECT c.criteria_id, c.max_score 
                         FROM score_table s
                         JOIN criteria_table c ON s.fk_score_criteria = c.criteria_id
                         WHERE s.score_id = ?";
        $criteria_stmt = $conn->prepare($criteria_query);
        $criteria_stmt->bind_param("i", $score_id);
        $criteria_stmt->execute();
        $criteria_result = $criteria_stmt->get_result();
        $criteria_data = $criteria_result->fetch_assoc();

        // Validate score value
        if ($score_value < 0 || $score_value > $criteria_data['max_score']) {
            $_SESSION['error'] = "Score must be between 0 and " . $criteria_data['max_score'];
            $conn->rollback();
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

        // Update score
        $query = "UPDATE score_table SET score_value = ?, remarks = ? WHERE score_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("dsi", $score_value, $remarks, $score_id);
        $stmt->execute();

        // Log the action
        $log_query = "INSERT INTO activity_log (username, action, details) VALUES (?, 'Update Score', ?)";
        $log_stmt = $conn->prepare($log_query);
        $details = "Updated score ID: $score_id, New score: $score_value";
        $log_stmt->bind_param("ss", $_SESSION['username'], $details);
        $log_stmt->execute();

        $conn->commit();
        $_SESSION['message'] = "Score updated successfully";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Error updating score: " . $e->getMessage();
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// Delete Score
if (isset($_GET['id'])) {
    $score_id = intval($_GET['id']);

    try {
        // Start transaction
        $conn->begin_transaction();

        // Delete the score
        $query = "DELETE FROM score_table WHERE score_id = $score_id";
        if ($conn->query($query)) {
            // Get the maximum ID after deletion
            $max_id_query = "SELECT MAX(score_id) as max_id FROM score_table";
            $result = $conn->query($max_id_query);
            $max_id = ($result->fetch_assoc())['max_id'] ?? 0;
            
            // Reset auto-increment to max_id + 1
            $reset_auto_increment = "ALTER TABLE score_table AUTO_INCREMENT = " . ($max_id + 1);
            $conn->query($reset_auto_increment);

            // Commit transaction
            $conn->commit();
            
            $_SESSION['success'] = "Score deleted successfully!";
        } else {
            throw new Exception($conn->error);
        }
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $_SESSION['error'] = "Error deleting score: " . $e->getMessage();
    }
    
    header("Location: admin_dashboard.php?page=scores");
    exit();
}

// If no valid action is performed, redirect back
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
?> 