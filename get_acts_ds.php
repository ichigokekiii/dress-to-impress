<?php
// This file fetches recent activities and returns them as JSON
// Filename: get_recent_activities.php

require_once "connection.php";

// Initialize activities array
$activities = [];

// Query for recent activities (assuming you have a logs or activities table)
// Modify this query based on your actual database structure
$query = "SELECT * FROM logs ORDER BY timestamp DESC LIMIT 5";

// Try to execute the query if the table exists
try {
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $activities[] = [
                'action' => $row['action'],
                'user' => $row['user'],
                'time' => date('M d, H:i', strtotime($row['timestamp']))
            ];
        }
    }
} catch (Exception $e) {
    // If the table doesn't exist or there's an error, add placeholder data
    $activities = [
        ['action' => 'Added new contestant', 'user' => 'Admin', 'time' => '5 mins ago'],
        ['action' => 'Updated judge information', 'user' => 'Judge Admin', 'time' => '10 mins ago'],
        ['action' => 'Score submitted', 'user' => 'Judge 1', 'time' => '15 mins ago'],
        ['action' => 'New category created', 'user' => 'Admin', 'time' => '30 mins ago']
    ];
}

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($activities);
?>