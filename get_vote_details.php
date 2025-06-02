<?php
require_once "connection.php";

// Get contestant ID from URL
$contestant_id = isset($_GET['contestant_id']) ? intval($_GET['contestant_id']) : 0;

if (!$contestant_id) {
    echo json_encode(['error' => 'Invalid contestant ID']);
    exit();
}

// Fetch criteria scores for the contestant
$criteria_query = "
    SELECT 
        ct.criteria_name,
        COALESCE(AVG(s.score_value), 0) as average_score,
        COUNT(DISTINCT s.fk_score_judge) as vote_count
    FROM criteria_table ct
    LEFT JOIN score_table s ON ct.criteria_id = s.fk_score_criteria 
        AND s.fk_score_contestant = ?
    WHERE ct.fk_criteria_contest = (
        SELECT fk_contestant_contest 
        FROM contestant_table 
        WHERE contestant_id = ?
    )
    GROUP BY ct.criteria_id, ct.criteria_name
    ORDER BY ct.criteria_id";

$stmt = $conn->prepare($criteria_query);
$stmt->bind_param("ii", $contestant_id, $contestant_id);
$stmt->execute();
$criteria_result = $stmt->get_result();

$criteria = [];
$total_score = 0;
$total_votes = 0;

while ($row = $criteria_result->fetch_assoc()) {
    $criteria[] = [
        'name' => $row['criteria_name'],
        'average_score' => floatval($row['average_score']),
        'vote_count' => intval($row['vote_count'])
    ];
    $total_score += floatval($row['average_score']);
    $total_votes = max($total_votes, intval($row['vote_count']));
}

$total_score = count($criteria) > 0 ? $total_score / count($criteria) : 0;

echo json_encode([
    'criteria' => $criteria,
    'total_score' => $total_score,
    'total_votes' => $total_votes
]); 