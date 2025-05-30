<?php
require_once "connection.php";

// Since we're not using login, we'll need to simulate a judge session
// You can replace this with a simple selection mechanism
if (!isset($_GET['judge_id'])) {
    // Default to judge ID 1 or redirect to a judge selection page
    $judge_id = 2;
} else {
    $judge_id = $_GET['judge_id'];
}

// Get judge information
$judge_query = "SELECT * FROM judge_table WHERE judge_id = ?";
$stmt = $conn->prepare($judge_query);
$stmt->bind_param("i", $judge_id);
$stmt->execute();
$judge_result = $stmt->get_result();
$judge = $judge_result->fetch_assoc();

// If judge doesn't exist, show a more helpful error
if (!$judge) {
    // Check if there are any judges at all
    $check_judges = "SELECT * FROM judge_table LIMIT 5";
    $check_result = $conn->query($check_judges);
    
    echo "<div class='container mt-5'>";
    echo "<div class='alert alert-danger'>";
    echo "<h4>Judge not found with ID: " . $judge_id . "</h4>";
    
    if ($check_result && $check_result->num_rows > 0) {
        echo "<p>Available judges in the database:</p>";
        echo "<ul>";
        while ($j = $check_result->fetch_assoc()) {
            echo "<li>ID: " . $j['judge_id'] . " - Name: " . $j['judge_name'] . "</li>";
        }
        echo "</ul>";
        echo "<p>Please select one of these judges using the URL parameter: judge_scoring.php?judge_id=X</p>";
    } else {
        echo "<p>No judges found in the database. Please add judges through the admin panel.</p>";
    }
    
    echo "<a href='admin_dashboard.php?page=judges' class='btn btn-primary'>Go to Admin Panel</a>";
    echo "</div>";
    echo "</div>";
    exit();
}

// Get all contestants
$contestants_query = "SELECT * FROM contestant_table ORDER BY contestant_number";
$contestants_result = $conn->query($contestants_query);

// Get criteria
$criteria_query = "SELECT * FROM criteria_table";
$criteria_result = $conn->query($criteria_query);

// Process score submission
if (isset($_POST['submit_scores'])) {
    $contestant_id = $_POST['contestant_id'];
    $score_value = $_POST['score_value'];
    
    // Check if score already exists
    $check_query = "SELECT * FROM score_table WHERE judge_id = ? AND contestant_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ii", $judge_id, $contestant_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        // Update existing score
        $update_query = "UPDATE score_table SET score_value = ? WHERE judge_id = ? AND contestant_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("dii", $score_value, $judge_id, $contestant_id);
        
        if ($update_stmt->execute()) {
            $success_message = "Score updated successfully!";
        } else {
            $error_message = "Error updating score: " . $conn->error;
        }
    } else {
        // Insert new score
        $insert_query = "INSERT INTO score_table (judge_id, contestant_id, score_value) VALUES (?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("iid", $judge_id, $contestant_id, $score_value);
        
        if ($insert_stmt->execute()) {
            $success_message = "Score submitted successfully!";
        } else {
            $error_message = "Error submitting score: " . $conn->error;
        }
    }
}

// Get existing scores for this judge
$scores_query = "SELECT s.*, c.contestant_name, c.contestant_number 
                FROM score_table s 
                JOIN contestant_table c ON s.contestant_id = c.contestant_id 
                WHERE s.judge_id = ?";
$scores_stmt = $conn->prepare($scores_query);
$scores_stmt->bind_param("i", $judge_id);
$scores_stmt->execute();
$scores_result = $scores_stmt->get_result();

$scores = [];
while ($row = $scores_result->fetch_assoc()) {
    $scores[$row['contestant_id']] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Judge Scoring Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 20px;
        }
        .judge-header {
            background-color: #343a40;
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .contestant-card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        .contestant-card:hover {
            transform: translateY(-5px);
        }
        .card-header {
            background-color: #6c757d;
            color: white;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        .score-input {
            font-size: 24px;
            text-align: center;
            width: 100px;
        }
        .score-submitted {
            background-color: #d4edda;
        }
        .score-range {
            width: 100%;
        }
        .score-display {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
        }
        .nav-tabs .nav-link.active {
            font-weight: bold;
            background-color: #e9ecef;
        }
        .judge-selector {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="judge-header text-center">
            <h1><i class="fas fa-gavel me-2"></i> Judge Scoring Panel</h1>
            <h3>Welcome, <?php echo $judge['judge_name']; ?></h3>
        </div>
        
        <!-- Judge Selector -->
        <div class="judge-selector text-center mb-4">
            <form action="" method="GET" class="d-inline-flex">
                <select name="judge_id" class="form-select me-2" onchange="this.form.submit()">
                    <?php
                    $all_judges_query = "SELECT * FROM judge_table ORDER BY judge_name";
                    $all_judges_result = $conn->query($all_judges_query);
                    while ($j = $all_judges_result->fetch_assoc()) {
                        $selected = ($j['judge_id'] == $judge_id) ? 'selected' : '';
                        echo "<option value='{$j['judge_id']}' {$selected}>{$j['judge_name']}</option>";
                    }
                    ?>
                </select>
                <button type="submit" class="btn btn-primary">Switch Judge</button>
            </form>
        </div>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> <?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <ul class="nav nav-tabs mb-4" id="contestantTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">
                    All Contestants
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="scored-tab" data-bs-toggle="tab" data-bs-target="#scored" type="button" role="tab" aria-controls="scored" aria-selected="false">
                    Scored Contestants
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="unscored-tab" data-bs-toggle="tab" data-bs-target="#unscored" type="button" role="tab" aria-controls="unscored" aria-selected="false">
                    Unscored Contestants
                </button>
            </li>
        </ul>
        
        <div class="tab-content" id="contestantTabsContent">
            <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                <div class="row">
                    <?php while ($contestant = $contestants_result->fetch_assoc()): ?>
                        <?php 
                        $has_score = isset($scores[$contestant['contestant_id']]);
                        $card_class = $has_score ? 'score-submitted' : '';
                        $current_score = $has_score ? $scores[$contestant['contestant_id']]['score_value'] : 0;
                        ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card contestant-card <?php echo $card_class; ?>">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        #<?php echo $contestant['contestant_number']; ?> - 
                                        <?php echo $contestant['contestant_name']; ?>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Category:</strong> <?php echo $contestant['category']; ?></p>
                                    <p><?php echo $contestant['descript']; ?></p>
                                    
                                    <form action="" method="POST" class="score-form">
                                        <input type="hidden" name="contestant_id" value="<?php echo $contestant['contestant_id']; ?>">
                                        
                                        <div class="mb-3 text-center">
                                            <label for="score_<?php echo $contestant['contestant_id']; ?>" class="form-label">Score (1-10):</label>
                                            <div class="d-flex justify-content-center align-items-center">
                                                <input type="range" class="form-range score-range me-3" 
                                                    id="score_range_<?php echo $contestant['contestant_id']; ?>" 
                                                    min="1" max="10" step="0.1" 
                                                    value="<?php echo $current_score ?: 5; ?>"
                                                    oninput="updateScoreInput(<?php echo $contestant['contestant_id']; ?>, this.value)">
                                                <input type="number" class="form-control score-input" 
                                                    id="score_<?php echo $contestant['contestant_id']; ?>" 
                                                    name="score_value" 
                                                    min="1" max="10" step="0.1" 
                                                    value="<?php echo $current_score ?: 5; ?>"
                                                    oninput="updateScoreRange(<?php echo $contestant['contestant_id']; ?>, this.value)">
                                            </div>
                                        </div>
                                        
                                        <div class="d-grid">
                                            <button type="submit" name="submit_scores" class="btn btn-primary">
                                                <?php echo $has_score ? 'Update Score' : 'Submit Score'; ?>
                                            </button>
                                        </div>
                                        
                                        <?php if ($has_score): ?>
                                            <div class="text-center mt-3">
                                                <p class="mb-0">Current Score:</p>
                                                <div class="score-display"><?php echo $current_score; ?></div>
                                                <p class="text-muted small">Last updated: 
                                                    <?php echo date('M d, Y h:i A', strtotime($scores[$contestant['contestant_id']]['timestamp'] ?? 'now')); ?>
                                                </p>
                                            </div>
                                        <?php endif; ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
            
            <!-- Scored tab content -->
            <div class="tab-pane fade" id="scored" role="tabpanel" aria-labelledby="scored-tab">
                <div class="row">
                    <?php 
                    // Reset the contestants result pointer
                    $contestants_result->data_seek(0);
                    while ($contestant = $contestants_result->fetch_assoc()): 
                        if (isset($scores[$contestant['contestant_id']])):
                            $current_score = $scores[$contestant['contestant_id']]['score_value'];
                    ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card contestant-card score-submitted">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        #<?php echo $contestant['contestant_number']; ?> - 
                                        <?php echo $contestant['contestant_name']; ?>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Category:</strong> <?php echo $contestant['category']; ?></p>
                                    <p><?php echo $contestant['description']; ?></p>
                                    
                                    <form action="" method="POST" class="score-form">
                                        <input type="hidden" name="contestant_id" value="<?php echo $contestant['contestant_id']; ?>">
                                        
                                        <div class="mb-3 text-center">
                                            <label for="score_s_<?php echo $contestant['contestant_id']; ?>" class="form-label">Score (1-10):</label>
                                            <div class="d-flex justify-content-center align-items-center">
                                                <input type="range" class="form-range score-range me-3" 
                                                    id="score_range_s_<?php echo $contestant['contestant_id']; ?>" 
                                                    min="1" max="10" step="0.1" 
                                                    value="<?php echo $current_score; ?>"
                                                    oninput="updateScoreInput('s_<?php echo $contestant['contestant_id']; ?>', this.value)">
                                                <input type="number" class="form-control score-input" 
                                                    id="score_s_<?php echo $contestant['contestant_id']; ?>" 
                                                    name="score_value" 
                                                    min="1" max="10" step="0.1" 
                                                    value="<?php echo $current_score; ?>"
                                                    oninput="updateScoreRange('s_<?php echo $contestant['contestant_id']; ?>', this.value)">
                                            </div>
                                        </div>
                                        
                                        <div class="d-grid">
                                            <button type="submit" name="submit_scores" class="btn btn-primary">
                                                Update Score
                                            </button>
                                        </div>
                                        
                                        <div class="text-center mt-3">
                                            <p class="mb-0">Current Score:</p>
                                            <div class="score-display"><?php echo $current_score; ?></div>
                                            <p class="text-muted small">Last updated: 
                                                <?php echo date('M d, Y h:i A', strtotime($scores[$contestant['contestant_id']]['timestamp'] ?? 'now')); ?>
                                            </p>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php 
                        endif;
                    endwhile; 
                    ?>
                </div>
            </div>
            
            <!-- Unscored tab content -->
            <div class="tab-pane fade" id="unscored" role="tabpanel" aria-labelledby="unscored-tab">
                <div class="row">
                    <?php 
                    // Reset the contestants result pointer
                    $contestants_result->data_seek(0);
                    while ($contestant = $contestants_result->fetch_assoc()): 
                        if (!isset($scores[$contestant['contestant_id']])):
                    ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card contestant-card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        #<?php echo $contestant['contestant_number']; ?> - 
                                        <?php echo $contestant['contestant_name']; ?>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Category:</strong> <?php echo $contestant['category']; ?></p>
                                    <p><?php echo $contestant['descript']; ?></p>
                                    
                                    <form action="" method="POST" class="score-form">
                                        <input type="hidden" name="contestant_id" value="<?php echo $contestant['contestant_id']; ?>">
                                        
                                        <div class="mb-3 text-center">
                                            <label for="score_u_<?php echo $contestant['contestant_id']; ?>" class="form-label">Score (1-10):</label>
                                            <div class="d-flex justify-content-center align-items-center">
                                                <input type="range" class="form-range score-range me-3" 
                                                    id="score_range_u_<?php echo $contestant['contestant_id']; ?>" 
                                                    min="1" max="10" step="0.1" 
                                                    value="5"
                                                    oninput="updateScoreInput('u_<?php echo $contestant['contestant_id']; ?>', this.value)">
                                                <input type="number" class="form-control score-input" 
                                                    id="score_u_<?php echo $contestant['contestant_id']; ?>" 
                                                    name="score_value" 
                                                    min="1" max="10" step="0.1" 
                                                    value="5"
                                                    oninput="updateScoreRange('u_<?php echo $contestant['contestant_id']; ?>', this.value)">
                                            </div>
                                        </div>
                                        
                                        <div class="d-grid">
                                            <button type="submit" name="submit_scores" class="btn btn-primary">
                                                Submit Score
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php 
                        endif;
                    endwhile; 
                    ?>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4 mb-5">
            <a href="admin_dashboard.php" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
            </a>
            <a href="index.php" class="btn btn-danger">
                <i class="fas fa-home me-2"></i> Home
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function updateScoreInput(id, value) {
            document.getElementById('score_' + id).value = value;
        }
        
        function updateScoreRange(id, value) {
            document.getElementById('score_range_' + id).value = value;
        }
        
        // Form submission with confirmation
        document.querySelectorAll('.score-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const contestantId = this.querySelector('input[name="contestant_id"]').value;
                const scoreValue = this.querySelector('input[name="score_value"]').value;
                const formData = new FormData(this);
                
                Swal.fire({
                    title: 'Confirm Score',
                    text: `Are you sure you want to submit a score of ${scoreValue}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#dc3545',
                    confirmButtonText: 'Yes, submit score!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    </script>
</body>
</html>