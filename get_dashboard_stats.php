<?php
// This file fetches dashboard statistics and returns them as JSON
// Filename: get_dashboard_stats.php

require_once "connection.php";

// Initialize the response array
$response = [
    'users' => 0,
    'tables' => 0,
    'contestants' => 0,
    'judges' => 0,
    'organizers' => 0,
    'votes' => 0
];

// Get Total Users
$userQuery = "SELECT COUNT(*) as count FROM users";
$userResult = $conn->query($userQuery);
if ($userResult) {
    $row = $userResult->fetch_assoc();
    $response['users'] = $row['count'];
}

// Get Total Tables (categories)
$tableQuery = "SELECT COUNT(*) as count FROM category_table";
$tableResult = $conn->query($tableQuery);
if ($tableResult) {
    $row = $tableResult->fetch_assoc();
    $response['tables'] = $row['count'];
}

// Get Total Contestants
$contestantQuery = "SELECT COUNT(*) as count FROM contestant_table";
$contestantResult = $conn->query($contestantQuery);
if ($contestantResult) {
    $row = $contestantResult->fetch_assoc();
    $response['contestants'] = $row['count'];
}

// Get Total Judges
$judgeQuery = "SELECT COUNT(*) as count FROM judge_table";
$judgeResult = $conn->query($judgeQuery);
if ($judgeResult) {
    $row = $judgeResult->fetch_assoc();
    $response['judges'] = $row['count'];
}

// Get Total Organizers
$organizerQuery = "SELECT COUNT(*) as count FROM organizers"; // Assuming this table exists
$organizerResult = $conn->query($organizerQuery);
if ($organizerResult) {
    $row = $organizerResult->fetch_assoc();
    $response['organizers'] = $row['count'];
} else {
    // If table doesn't exist, return 0 for organizers
    $response['organizers'] = 0;
}

// Get Total Votes
$voteQuery = "SELECT COUNT(*) as count FROM scores"; // Assuming this table exists for storing votes/scores
$voteResult = $conn->query($voteQuery);
if ($voteResult) {
    $row = $voteResult->fetch_assoc();
    $response['votes'] = $row['count'];
} else {
    // If table doesn't exist, return 0 for votes
    $response['votes'] = 0;
}

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>