<?php
session_start();
require_once "connection.php";

if (!isset($_SESSION['username']) || $_SESSION['userType'] !== 'Judge') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Only judges can submit scores']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['contestantId']) || !isset($data['scores'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit();
}

try {

    $conn->begin_transaction();

    $judge_query = "SELECT j.judge_id 
                   FROM judge_table j 
                   INNER JOIN users_table u ON j.judge_name = u.username 
                   WHERE u.username = ? AND u.userType = 'Judge'";
    $stmt = $conn->prepare($judge_query);
    $stmt->bind_param("s", $_SESSION['username']);
    $stmt->execute();
    $judge_result = $stmt->get_result();
    $judge = $judge_result->fetch_assoc();

    if (!$judge) {

        $insert_judge_query = "INSERT INTO judge_table (judge_name, contact_information) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_judge_query);
        $contact_info = "";
        $stmt->bind_param("ss", $_SESSION['username'], $contact_info);
        $stmt->execute();
        $judge_id = $conn->insert_id;
    } else {
        $judge_id = $judge['judge_id'];
    }

 
    $check_query = "SELECT COUNT(*) as count FROM score_table 
                   WHERE fk_score_judge = ? AND fk_score_contestant = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ii", $judge_id, $data['contestantId']);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result['count'] > 0) {
        throw new Exception('You have already voted for this contestant');
    }

    $insert_query = "INSERT INTO score_table (fk_score_judge, fk_score_contestant, fk_score_criteria, score_value) 
                     VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);

    foreach ($data['scores'] as $score) {
        $stmt->bind_param("iiid", 
            $judge_id,
            $data['contestantId'],
            $score['criteriaId'],
            $score['score']
        );
        $stmt->execute();
    }


    $contestant_query = "SELECT contestant_name, contestant_number FROM contestant_table WHERE contestant_id = ?";
    $stmt = $conn->prepare($contestant_query);
    $stmt->bind_param("i", $data['contestantId']);
    $stmt->execute();
    $contestant = $stmt->get_result()->fetch_assoc();

    $action = "Judge " . $_SESSION['username'] . " scored contestant " . $contestant['contestant_name'] . " (#" . $contestant['contestant_number'] . ")";
    $log_query = "INSERT INTO logs_table (action, log_time, fk_logs_users) VALUES (?, NOW(), ?)";
    $stmt = $conn->prepare($log_query);
    $stmt->bind_param("si", $action, $_SESSION['users_id']);
    $stmt->execute();

    $conn->commit();

    echo json_encode(['success' => true]);

} catch (Exception $e) {

    $conn->rollback();
    
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 


