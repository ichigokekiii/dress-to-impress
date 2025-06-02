<?php
session_start();
require_once "connection.php";

// Check if user is logged in and has appropriate permissions
if (!isset($_SESSION['username']) || !in_array($_SESSION['userType'], ['Admin', 'Staff'])) {
    $response = array(
        'status' => 'error',
        'message' => 'Unauthorized access'
    );
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Handle Add Score
if (isset($_POST['save_score'])) {
    $judge_id = intval($_POST['fk_score_judge']);
    $contestant_id = intval($_POST['fk_score_contestant']);
    $criteria_id = intval($_POST['fk_score_criteria']);
    $score_value = floatval($_POST['score_value']);
    $remarks = $conn->real_escape_string($_POST['remarks'] ?? '');

    // Check if score already exists
    $check_query = "SELECT score_id FROM score_table 
                   WHERE fk_score_judge = ? AND fk_score_contestant = ? AND fk_score_criteria = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("iii", $judge_id, $contestant_id, $criteria_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $response = array(
            'status' => 'error',
            'message' => 'Score already exists for this judge, contestant, and criteria combination'
        );
    } else {
        $query = "INSERT INTO score_table (fk_score_judge, fk_score_contestant, fk_score_criteria, score_value, remarks) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiids", $judge_id, $contestant_id, $criteria_id, $score_value, $remarks);

        if ($stmt->execute()) {
            $response = array(
                'status' => 'success',
                'message' => 'Score added successfully'
            );
        } else {
            $response = array(
                'status' => 'error',
                'message' => 'Error adding score: ' . $conn->error
            );
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Handle Update Score
if (isset($_POST['update_score'])) {
    $score_id = intval($_POST['score_id']);
    $judge_id = intval($_POST['fk_score_judge']);
    $contestant_id = intval($_POST['fk_score_contestant']);
    $criteria_id = intval($_POST['fk_score_criteria']);
    $score_value = floatval($_POST['score_value']);
    $remarks = $conn->real_escape_string($_POST['remarks'] ?? '');

    // Check if another score exists with the same combination (excluding current score_id)
    $check_query = "SELECT score_id FROM score_table 
                   WHERE fk_score_judge = ? AND fk_score_contestant = ? AND fk_score_criteria = ? 
                   AND score_id != ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("iiii", $judge_id, $contestant_id, $criteria_id, $score_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $response = array(
            'status' => 'error',
            'message' => 'Another score already exists for this judge, contestant, and criteria combination'
        );
    } else {
        $query = "UPDATE score_table 
                  SET fk_score_judge = ?, fk_score_contestant = ?, fk_score_criteria = ?, 
                      score_value = ?, remarks = ?
                  WHERE score_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiidsi", $judge_id, $contestant_id, $criteria_id, $score_value, $remarks, $score_id);

        if ($stmt->execute()) {
            $response = array(
                'status' => 'success',
                'message' => 'Score updated successfully'
            );
        } else {
            $response = array(
                'status' => 'error',
                'message' => 'Error updating score: ' . $conn->error
            );
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Handle Delete Score
if (isset($_GET['delete_score'])) {
    $score_id = intval($_GET['delete_score']);
    
    $query = "DELETE FROM score_table WHERE score_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $score_id);

    if ($stmt->execute()) {
        $response = array(
            'status' => 'success',
            'message' => 'Score deleted successfully'
        );
    } else {
        $response = array(
            'status' => 'error',
            'message' => 'Error deleting score: ' . $conn->error
        );
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?> 