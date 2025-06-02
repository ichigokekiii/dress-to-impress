<?php
session_start();
require_once "connection.php";
include "contestant_table_query.php";
include "category_table_query.php";
include "judge_table_query.php";

if (!isset($_SESSION['username']) || $_SESSION['userType'] !== 'Admin') {
	error_log("Access denied to admin dashboard - User: " . ($_SESSION['username'] ?? 'not set') . ", Type: " . ($_SESSION['userType'] ?? 'not set'));
	header("Location: login.php");
	exit();
}

$stats = array();

// Total Contestants
$result = $conn->query("SELECT COUNT(*) as total FROM contestant_table");
$stats['contestants'] = $result->fetch_assoc()['total'];

// Total Categories
$result = $conn->query("SELECT COUNT(*) as total FROM category_table");
$stats['categories'] = $result->fetch_assoc()['total'];

// Total Judges
$result = $conn->query("SELECT COUNT(*) as total FROM judge_table");
$stats['judges'] = $result->fetch_assoc()['total'];

// Recent Activities from logs
$result = $conn->query("SELECT action, log_time 
                       FROM logs_table 
                       ORDER BY log_time DESC LIMIT 5");
$recent_activities = $result->fetch_all(MYSQLI_ASSOC);

// Get upcoming contests
$result = $conn->query("SELECT contest_name, contest_date, location 
                       FROM contest_table 
                       WHERE contest_date >= CURDATE() 
                       ORDER BY contest_date ASC LIMIT 3");
$upcoming_contests = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin Dashboard</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
	<link rel="stylesheet" href="style.css">
</head>

<body>
	<div class="sidebar">
		<h4 class="p-3">Admin Dashboard</h4>
		<div class="user-info p-3 text-white">
			<small>Welcome,</small>
			<div><?php echo htmlspecialchars($_SESSION['username']); ?></div>
		</div>
		<a href="#overview" onclick="showPage('overview')"><i class="fas fa-home"></i> Overview</a>
		<a href="#contests" onclick="showPage('contests')"><i class="fas fa-trophy"></i> Contest Table</a>
		<a href="#contestants" onclick="showPage('contestants')"><i class="fas fa-users"></i> Contestant Table</a>
		<a href="#categories" onclick="showPage('categories')"><i class="fas fa-list"></i> Category Table</a>
		<a href="#judges" onclick="showPage('judges')"><i class="fas fa-gavel"></i> Judge Table</a>
		<a href="#scores" onclick="showPage('scores')"><i class="fas fa-star"></i> Score Table</a>
		<a href="#criteria" onclick="showPage('criteria')"><i class="fas fa-tasks"></i> Criteria Table</a>
		<a href="#users" onclick="showPage('users')"><i class="fas fa-user"></i> Users</a>
		<a href="#logs" onclick="showPage('logs')"><i class="fas fa-history"></i> Logs</a>
		<a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
	</div>

	<div class="content">
		<div id="overview">
			<h2 class="mb-4">Overview</h2>
			
			<!-- Statistics Cards -->
			<div class="row mb-4">
				<div class="col-md-3">
					<div class="card bg-primary text-white">
						<div class="card-body">
							<div class="d-flex justify-content-between align-items-center">
								<div>
									<h6 class="card-title">Total Contestants</h6>
									<h2 class="mb-0"><?php echo $stats['contestants']; ?></h2>
								</div>
								<i class="fas fa-users fa-2x opacity-50"></i>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="card bg-success text-white">
						<div class="card-body">
							<div class="d-flex justify-content-between align-items-center">
								<div>
									<h6 class="card-title">Total Categories</h6>
									<h2 class="mb-0"><?php echo $stats['categories']; ?></h2>
								</div>
								<i class="fas fa-list fa-2x opacity-50"></i>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="card bg-info text-white">
						<div class="card-body">
							<div class="d-flex justify-content-between align-items-center">
								<div>
									<h6 class="card-title">Total Judges</h6>
									<h2 class="mb-0"><?php echo $stats['judges']; ?></h2>
								</div>
								<i class="fas fa-gavel fa-2x opacity-50"></i>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="card <?php echo $conn->ping() ? 'bg-success' : 'bg-danger'; ?> text-white">
						<div class="card-body">
							<div class="d-flex justify-content-between align-items-center">
								<div>
									<h6 class="card-title">Database Status</h6>
									<div class="d-flex align-items-center">
										<span class="status-dot <?php echo $conn->ping() ? 'active' : ''; ?>"></span>
										<h2 class="mb-0 ms-2"><?php echo $conn->ping() ? 'Connected' : 'Disconnected'; ?></h2>
									</div>
									<small>Host: <?php echo $conn->host_info; ?></small>
								</div>
								<i class="fas fa-database fa-2x opacity-50"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<!-- Upcoming Contests -->
	
			<div class="row mb-4">
				<div class="col-md-6">
					<div class="card">
						<div class="card-header">
							<h5 class="card-title mb-0">Upcoming Contests</h5>
						</div>
						<div class="card-body">
							<?php if (empty($upcoming_contests)): ?>
								<p class="text-muted">No upcoming contests</p>
							<?php else: ?>
								<div class="list-group list-group-flush">
									<?php foreach ($upcoming_contests as $contest): ?>
										<div class="list-group-item">
											<div class="d-flex w-100 justify-content-between">
												<h6 class="mb-1"><?php echo htmlspecialchars($contest['contest_name']); ?></h6>
												<small><?php echo date('M d, Y', strtotime($contest['contest_date'])); ?></small>
											</div>
											<small class="text-muted">
												<i class="fas fa-map-marker-alt"></i> 
												<?php echo htmlspecialchars($contest['location']); ?>
											</small>
										</div>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Contests -->
		<div id="contests" class="d-none">
			<h2>Contests</h2>
			<button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addContestModal">Add Contest</button>
			<?php include 'contest_section.php'; ?>
		</div>

		<div id="contestants" class="d-none">
			<h2>Contestants</h2>
			<button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addContestantModal">Add Contestant</button>
			<div class="modal fade" id="addContestantModal" tabindex="-1" aria-labelledby="addContestantModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="addContestantModalLabel">Add Contestant</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<form action="contestant_table_query.php" method="POST">
								<div class="row mb-3">
									<div class="col">
										<label for="contestant_name" class="form-label">Contestant Name</label>
										<input type="text" class="form-control" id="contestant_name" name="contestant_name" required>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col">
										<label for="contestant_number" class="form-label">Contestant Number</label>
										<input type="number" class="form-control" id="contestant_number" name="contestant_number" required>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col">
										<label for="contest" class="form-label">Contest</label>
										<select class="form-select" id="contest" name="contest" required>
											<?php
											$contest_query = "SELECT contest_id, contest_name FROM contest_table ORDER BY contest_name";
											$contest_result = $conn->query($contest_query);
											while ($contest = $contest_result->fetch_assoc()) {
												echo "<option value='" . $contest['contest_id'] . "'>" . htmlspecialchars($contest['contest_name']) . "</option>";
											}
											?>
										</select>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col">
										<label for="category" class="form-label">Category</label>
										<select class="form-select" id="category" name="category" required>
											<?php
											$category_query = "SELECT category_id, category_name FROM category_table ORDER BY category_name";
											$category_result = $conn->query($category_query);
											while ($category = $category_result->fetch_assoc()) {
												echo "<option value='" . $category['category_id'] . "'>" . htmlspecialchars($category['category_name']) . "</option>";
											}
											?>
										</select>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col">
										<label for="title" class="form-label">Title</label>
										<input type="text" class="form-control" id="title" name="title" required>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col">
										<label for="bio" class="form-label">Bio</label>
										<textarea class="form-control" id="bio" name="bio" rows="4" required></textarea>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col">
										<label for="gender" class="form-label">Gender</label>
										<select class="form-select" id="gender" name="gender" required>
											<option value="Male">Male</option>
											<option value="Female">Female</option>
										</select>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col">
										<label for="expanded_image" class="form-label">Expanded Image</label>
										<input type="file" class="form-control" id="expanded_image" name="expanded_image" accept="image/*">
										<small class="text-muted">Recommended size: 400x300 pixels</small>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col">
										<label for="voting_image" class="form-label">Voting Image</label>
										<input type="file" class="form-control" id="voting_image" name="voting_image" accept="image/*">
										<small class="text-muted">Recommended size: 800x1000 pixels (Full body photo)</small>
									</div>
								</div>
								<div class="row mt-4">
									<div class="col text-end">
										<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
										<button type="submit" class="btn btn-primary" name="save_contestant">Save Contestant</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<input type="text" class="form-control search-box" placeholder="Search Contestants..." onkeyup="searchTable('contestantTable', this.value)">

			<?php
			$query = "SELECT c.*, ct.contest_name, cat.category_name 
					 FROM contestant_table c
					 LEFT JOIN contest_table ct ON c.fk_contestant_contest = ct.contest_id
					 LEFT JOIN category_table cat ON c.fk_contestant_category = cat.category_id
					 ORDER BY ct.contest_name, c.contestant_number";
			$query_run = $conn->query($query);
			?>
			<table class="table table-bordered" id="contestantTable">
				<thead>
					<tr>
						<th>ID</th>
						<th>Contest</th>
						<th>Number</th>
						<th>Name</th>
						<th>Category</th>
						<th>Title</th>
						<th>Bio</th>
						<th>Gender</th>
						<th style="width: 15%;">Action</th>
					</tr>
				</thead>
				<tbody>
				<?php
				if ($query_run) {
					while ($row = mysqli_fetch_array($query_run)) {
						echo "<tr>";
						echo "<td>" . $row['contestant_id'] . "</td>";
						echo "<td>" . htmlspecialchars($row['contest_name']) . "</td>";
						echo "<td>" . $row['contestant_number'] . "</td>";
						echo "<td>" . htmlspecialchars($row['contestant_name']) . "</td>";
						echo "<td>" . htmlspecialchars($row['category_name']) . "</td>";
						echo "<td>" . htmlspecialchars($row['title']) . "</td>";
						echo "<td>" . htmlspecialchars($row['bio']) . "</td>";
						echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
						echo "<td>";
						echo "<a href='#' class='btn btn-success btn-sm me-1'
								data-bs-toggle='modal'
								data-bs-target='#editContestantModal'
								data-id='" . $row['contestant_id'] . "'
								data-name='" . htmlspecialchars($row['contestant_name'], ENT_QUOTES) . "'
								data-number='" . $row['contestant_number'] . "'
								data-contest='" . $row['fk_contestant_contest'] . "'
								data-category='" . $row['fk_contestant_category'] . "'
								data-title='" . htmlspecialchars($row['title'], ENT_QUOTES) . "'
								data-bio='" . htmlspecialchars($row['bio'], ENT_QUOTES) . "'
								data-gender='" . htmlspecialchars($row['gender'], ENT_QUOTES) . "'
								onclick='populateEditModal(this)'>
								Edit
							</a>";
						echo "<a href='#' class='btn btn-danger btn-sm' onclick='confirmDeleteContestant(" . $row['contestant_id'] . ")'>Delete</a>";
						echo "</td>";
						echo "</tr>";
					}
				}
				?>
				</tbody>
			</table>
		</div>

		<!-- Categories -->
		<div id="categories" class="d-none">
			<h2>Categories</h2>
			<button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addCategoryModal">Add Category</button>
			<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="addCategoryModalLabel">Add Category</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<form action="category_table_query.php" method="POST">
								<div class="row mb-3">
									<div class="col">
										<label for="category_name" class="form-label">Category Name</label>
										<input type="text" class="form-control" id="category_name" name="category_name" required>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col">
										<label for="contest" class="form-label">Contest</label>
										<select class="form-select" id="contest" name="fk_category_contest" required>
											<?php
											$contest_query = "SELECT contest_id, contest_name FROM contest_table ORDER BY contest_name";
											$contest_result = $conn->query($contest_query);
											while ($contest = $contest_result->fetch_assoc()) {
												echo "<option value='" . $contest['contest_id'] . "'>" . htmlspecialchars($contest['contest_name']) . "</option>";
											}
											?>
										</select>
									</div>
								</div>
								<div class="row mb-3">
									<div class="col">
										<label for="category_description" class="form-label">Description</label>
										<textarea class="form-control" id="category_description" name="category_description" rows="3"></textarea>
									</div>
								</div>
								<div class="row mt-4">
									<div class="col text-end">
										<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
										<button type="submit" class="btn btn-primary" name="save_category">Save Category</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<input type="text" class="form-control search-box" placeholder="Search Category..." onkeyup="searchTable('categoryTable', this.value)">

			<?php
			$query = "SELECT c.*, ct.contest_name 
					  FROM category_table c
					  LEFT JOIN contest_table ct ON c.fk_category_contest = ct.contest_id
					  ORDER BY ct.contest_name, c.category_name";
			$query_run = $conn->query($query);
			?>
			<table class="table table-bordered" id="categoryTable">
				<thead>
					<tr>
					<th style="width: 10%;">Category Id</th>
					<th>Category Name</th>
						<th>Contest</th>
						<th>Description</th>
					<th style="width: 15%;">Action</th>
					</tr>
				</thead>
				<tbody>
				<?php
				if ($query_run) {
					while ($row = mysqli_fetch_array($query_run)) {
						echo "<tr>";
						echo "<td>" . $row['category_id'] . "</td>";
						echo "<td>" . htmlspecialchars($row['category_name']) . "</td>";
						echo "<td>" . htmlspecialchars($row['contest_name']) . "</td>";
						echo "<td>" . htmlspecialchars($row['category_description']) . "</td>";
						echo "<td class=''>";
						echo "<a href='#' class='btn btn-success'
								data-bs-toggle='modal'
								data-bs-target='#editCategoryModal'
								data-id='" . $row['category_id'] . "'
								data-name='" . htmlspecialchars($row['category_name'], ENT_QUOTES) . "'
								data-contest='" . $row['fk_category_contest'] . "'
								data-description='" . htmlspecialchars($row['category_description'], ENT_QUOTES) . "'
								onclick='populateEditCategoryModal(this)'>
										Edit
							</a>";
						echo "<a href='#' class='btn btn-danger' onclick='confirmDeleteCategory(" . $row['category_id'] . ")'>Delete</a>";
						echo "</td>";
						echo "</tr>";
				}
				}
				?>
				</tbody>
			</table>
		</div>

		<!-- Judges -->
		<div id="judges" class="d-none">
			<h2>Judges</h2>
			<button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addJudgeModal">Add Judge</button>
			<div class="modal fade" id="addJudgeModal" tabindex="-1" aria-labelledby="addJudgeModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="addJudgeModalLabel">Add Judge</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<form action="admin_dashboard.php" method="POST">
								<div class="row">
									<div class="col">
										<label for="judge_name" class="form-label">Judge Name</label>
										<input type="text" class="form-control" id="judge_name" name="judge_name" required>
									</div>
								</div>
								<div class="row">
									<div class="col">
										<label for="contact_information" class="form-label">Contact Information</label>
										<input type="text" class="form-control" id="contact_information" name="contact_information" required>
									</div>
								</div>

								<div class="row mt-4">
									<div class="col text-end">
										<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
										<button type="submit" class="btn btn-primary" name="save_judge">Save Judge</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<input type="text" class="form-control search-box" placeholder="Search Judge..." onkeyup="searchTable('judgeTable', this.value)">

			<?php
			$query = "SELECT * FROM judge_table";
			$query_run = $conn->query($query);
			?>
			<table class="table table-bordered" id="judgeTable">
				<thead>
					<th>Judge Id</th>
					<th>Judge Name</th>
					<th>Contact Information</th>
					<th style="width: 15%;">Action</th>
				</thead>

				<?php

				if ($query_run) {
					while ($row = mysqli_fetch_array($query_run)) {
				?>
						<tbody>
							<tr>
								<td><?php echo $row['judge_id']; ?></td>
								<td><?php echo $row['judge_name']; ?></td>
								<td><?php echo $row['contact_information']; ?></td>
								<td class="">
									<a href="#" class="btn btn-success"
										data-bs-toggle="modal"
										data-bs-target="#editJudgeModal"
										data-id="<?php echo $row['judge_id']; ?>"
										data-name="<?php echo $row['judge_name']; ?>"
										data-info="<?php echo $row['contact_information']; ?>"
										onclick="populateEditJudgeModal(this)">
										Edit
									</a>
									<a href="#" class="btn btn-danger" onclick="confirmDeleteJudge(<?php echo $row['judge_id']; ?>)">Delete</a>
								</td>
							</tr>
						</tbody>
				<?php
					}
				} else {
					echo "No record Found";
				}

				?>
			</table>
		</div>

		<!-- Scores -->
		<div id="scores" class="d-none">
			<h2>Score Table</h2>
			<button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addScoreModal">Add Score</button>
			<input type="text" class="form-control search-box" placeholder="Search Scores..." onkeyup="searchTable('scoreTable', this.value)">
			
			<?php
			$query = "SELECT s.*, j.judge_name, c.contestant_name, cr.criteria_name, cr.max_score 
					  FROM score_table s
					  LEFT JOIN judge_table j ON s.fk_score_judge = j.judge_id
					  LEFT JOIN contestant_table c ON s.fk_score_contestant = c.contestant_id
					  LEFT JOIN criteria_table cr ON s.fk_score_criteria = cr.criteria_id
					  ORDER BY j.judge_name, c.contestant_name";
			$query_run = $conn->query($query);
			?>
			<div class="table-container">
				<table class="table table-bordered" id="scoreTable">
					<thead>
						<tr>
							<th>ID</th>
							<th>Judge</th>
							<th>Contestant</th>
							<th>Criteria</th>
							<th>Score</th>
							<th>Max Score</th>
							<th>Remarks</th>
							<th style="width: 15%;">Action</th>
						</tr>
					</thead>
					<tbody>
					<?php
					if ($query_run) {
						while ($row = mysqli_fetch_array($query_run)) {
							echo "<tr>";
							echo "<td>" . $row['score_id'] . "</td>";
							echo "<td>" . htmlspecialchars($row['judge_name']) . "</td>";
							echo "<td>" . htmlspecialchars($row['contestant_name']) . "</td>";
							echo "<td>" . htmlspecialchars($row['criteria_name']) . "</td>";
							echo "<td>" . $row['score_value'] . "</td>";
							echo "<td>" . $row['max_score'] . "</td>";
							echo "<td>" . htmlspecialchars($row['remarks'] ?? '') . "</td>";
							echo "<td>";
							echo "<button type='button' class='btn btn-success btn-sm me-1' 
									data-bs-toggle='modal'
									data-bs-target='#editScoreModal'
									data-id='" . $row['score_id'] . "'
									data-score='" . $row['score_value'] . "'
									data-remarks='" . htmlspecialchars($row['remarks'] ?? '', ENT_QUOTES) . "'
									data-max-score='" . $row['max_score'] . "'>
									Edit
								</button>";
							echo "<button type='button' class='btn btn-danger btn-sm' onclick='confirmDeleteScore(" . $row['score_id'] . ")'>
									Delete
								</button>";
							echo "</td>";
							echo "</tr>";
						}
					}
					?>
					</tbody>
				</table>
			</div>

			<!-- Add Score Modal -->
			<div class="modal fade" id="addScoreModal" tabindex="-1" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">Add Score</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<form action="score_table_query.php" method="POST" id="addScoreForm">
							<div class="modal-body">
								<div class="mb-3">
									<label for="judge_id" class="form-label">Judge</label>
									<select class="form-select" id="judge_id" name="judge_id" required>
										<?php
										$judge_query = "SELECT judge_id, judge_name FROM judge_table ORDER BY judge_name";
										$judge_result = $conn->query($judge_query);
										while ($judge = $judge_result->fetch_assoc()) {
											echo "<option value='" . $judge['judge_id'] . "'>" . htmlspecialchars($judge['judge_name']) . "</option>";
										}
										?>
									</select>
								</div>
								<div class="mb-3">
									<label for="contestant_id" class="form-label">Contestant</label>
									<select class="form-select" id="contestant_id" name="contestant_id" required>
										<?php
										$contestant_query = "SELECT contestant_id, contestant_name, contestant_number FROM contestant_table ORDER BY contestant_name";
										$contestant_result = $conn->query($contestant_query);
										while ($contestant = $contestant_result->fetch_assoc()) {
											echo "<option value='" . $contestant['contestant_id'] . "'>#" . $contestant['contestant_number'] . " - " . htmlspecialchars($contestant['contestant_name']) . "</option>";
										}
										?>
									</select>
								</div>
								<div class="mb-3">
									<label for="criteria_id" class="form-label">Criteria</label>
									<select class="form-select" id="criteria_id" name="criteria_id" required>
										<?php
										$criteria_query = "SELECT criteria_id, criteria_name, max_score FROM criteria_table ORDER BY criteria_name";
										$criteria_result = $conn->query($criteria_query);
										while ($criteria = $criteria_result->fetch_assoc()) {
											echo "<option value='" . $criteria['criteria_id'] . "' data-max-score='" . $criteria['max_score'] . "'>" 
												. htmlspecialchars($criteria['criteria_name']) 
												. " (Max: " . $criteria['max_score'] . ")</option>";
										}
										?>
									</select>
								</div>
								<div class="mb-3">
									<label for="score_value" class="form-label">Score</label>
									<input type="number" class="form-control" id="score_value" name="score_value" step="0.01" required>
									<div id="maxScoreHelp" class="form-text">Maximum score: <span id="maxScoreDisplay">100</span></div>
								</div>
								<div class="mb-3">
									<label for="remarks" class="form-label">Remarks</label>
									<textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
								<button type="submit" class="btn btn-primary" name="save_score">Save Score</button>
							</div>
						</form>
					</div>
				</div>
			</div>

			<!-- Edit Score Modal -->
			<div class="modal fade" id="editScoreModal" tabindex="-1" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">Edit Score</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<form action="score_table_query.php" method="POST" id="editScoreForm">
							<div class="modal-body">
								<input type="hidden" id="edit_score_id" name="score_id">
								<div class="mb-3">
									<label for="edit_score_value" class="form-label">Score</label>
									<input type="number" class="form-control" id="edit_score_value" name="score_value" step="0.01" required>
									<div id="editMaxScoreHelp" class="form-text">Maximum score: <span id="editMaxScoreDisplay">100</span></div>
								</div>
								<div class="mb-3">
									<label for="edit_remarks" class="form-label">Remarks</label>
									<textarea class="form-control" id="edit_remarks" name="remarks" rows="3"></textarea>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
								<button type="submit" class="btn btn-primary" name="update_score">Update Score</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<!-- Criteria -->
		<div id="criteria" class="d-none">
			<h2>Criteria Table</h2>
			<button type="button" class="btn btn-primary mb-2" id="addCriteriaBtn">Add Criteria</button>

			<input type="text" class="form-control search-box mb-3" placeholder="Search Criteria..." onkeyup="searchTable('criteriaTable', this.value)">
			
			<?php
			$query = "SELECT c.*, ct.contest_name 
					  FROM criteria_table c
					  LEFT JOIN contest_table ct ON c.fk_criteria_contest = ct.contest_id
					  ORDER BY ct.contest_name, c.criteria_name";
			$query_run = $conn->query($query);
			?>
			<table class="table table-bordered" id="criteriaTable">
				<thead>
					<tr>
						<th>ID</th>
						<th>Contest</th>
						<th>Criteria Name</th>
						<th>Description</th>
						<th>Max Score</th>
						<th style="width: 15%;">Action</th>
					</tr>
				</thead>
				<tbody>
				<?php
				if ($query_run) {
					while ($row = mysqli_fetch_array($query_run)) {
						echo "<tr>";
						echo "<td>" . $row['criteria_id'] . "</td>";
						echo "<td>" . htmlspecialchars($row['contest_name']) . "</td>";
						echo "<td>" . htmlspecialchars($row['criteria_name']) . "</td>";
						echo "<td>" . htmlspecialchars($row['criteria_description']) . "</td>";
						echo "<td>" . $row['max_score'] . "</td>";
						echo "<td>";
						echo "<button type='button' class='btn btn-success btn-sm me-1' 
								data-bs-toggle='modal'
								data-bs-target='#editCriteriaModal'
								data-id='" . $row['criteria_id'] . "'
								data-name='" . htmlspecialchars($row['criteria_name'], ENT_QUOTES) . "'
								data-contest='" . $row['fk_criteria_contest'] . "'
								data-description='" . htmlspecialchars($row['criteria_description'], ENT_QUOTES) . "'
								data-max-score='" . $row['max_score'] . "'>
								Edit
							</button>";
						echo "<button type='button' class='btn btn-danger btn-sm' onclick='confirmDeleteCriteria(" . $row['criteria_id'] . ")'>
								Delete
							</button>";
						echo "</td>";
						echo "</tr>";
					}
				}
				?>
				</tbody>
			</table>

			<!-- Add Criteria Modal -->
			<div class="modal fade" id="addCriteriaModal" tabindex="-1" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">Add Criteria</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<form action="criteria_table_query.php" method="POST" id="addCriteriaForm">
							<div class="modal-body">
								<div class="mb-3">
									<label for="criteria_name" class="form-label">Criteria Name</label>
									<input type="text" class="form-control" id="criteria_name" name="criteria_name" required>
								</div>
								<div class="mb-3">
									<label for="contest" class="form-label">Contest</label>
									<select class="form-select" id="contest" name="fk_criteria_contest" required>
										<?php
										$contest_query = "SELECT contest_id, contest_name FROM contest_table ORDER BY contest_name";
										$contest_result = $conn->query($contest_query);
										while ($contest = $contest_result->fetch_assoc()) {
											echo "<option value='" . $contest['contest_id'] . "'>" . htmlspecialchars($contest['contest_name']) . "</option>";
										}
										?>
									</select>
								</div>
								<div class="mb-3">
									<label for="criteria_description" class="form-label">Description</label>
									<textarea class="form-control" id="criteria_description" name="criteria_description" rows="3"></textarea>
								</div>
								<div class="mb-3">
									<label for="max_score" class="form-label">Maximum Score</label>
									<input type="number" class="form-control" id="max_score" name="max_score" min="1" max="100" value="100" required>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
								<button type="submit" class="btn btn-primary" name="save_criteria">Save Criteria</button>
							</div>
						</form>
					</div>
				</div>
			</div>

			<!-- Edit Criteria Modal -->
			<div class="modal fade" id="editCriteriaModal" tabindex="-1" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">Edit Criteria</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<form action="criteria_table_query.php" method="POST" id="editCriteriaForm">
							<div class="modal-body">
								<input type="hidden" id="edit_criteria_id" name="criteria_id">
								<div class="mb-3">
									<label for="edit_criteria_name" class="form-label">Criteria Name</label>
									<input type="text" class="form-control" id="edit_criteria_name" name="criteria_name" required>
								</div>
								<div class="mb-3">
									<label for="edit_contest" class="form-label">Contest</label>
									<select class="form-select" id="edit_contest" name="fk_criteria_contest" required>
										<?php
										$contest_query = "SELECT contest_id, contest_name FROM contest_table ORDER BY contest_name";
										$contest_result = $conn->query($contest_query);
										while ($contest = $contest_result->fetch_assoc()) {
											echo "<option value='" . $contest['contest_id'] . "'>" . htmlspecialchars($contest['contest_name']) . "</option>";
										}
										?>
									</select>
								</div>
								<div class="mb-3">
									<label for="edit_criteria_description" class="form-label">Description</label>
									<textarea class="form-control" id="edit_criteria_description" name="criteria_description" rows="3"></textarea>
								</div>
								<div class="mb-3">
									<label for="edit_max_score" class="form-label">Maximum Score</label>
									<input type="number" class="form-control" id="edit_max_score" name="max_score" min="1" max="100" required>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
								<button type="submit" class="btn btn-primary" name="update_criteria">Update Criteria</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<!-- Users -->
		<div id="users" class="d-none">
			<h2>Users</h2>
			<!-- Manage users -->
		</div>

		<!-- Logs -->
		<div id="logs" class="d-none">
			<h2>Logs</h2>
			<!-- View logs -->
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			// Check URL parameters for page to show
			const urlParams = new URLSearchParams(window.location.search);
			const pageToShow = urlParams.get('page');
			if (pageToShow) {
				showPage(pageToShow);
			}

			// Initialize Bootstrap modals
			const addCriteriaModal = new bootstrap.Modal(document.getElementById('addCriteriaModal'));
			const editCriteriaModal = new bootstrap.Modal(document.getElementById('editCriteriaModal'));

			// Add Criteria button click handler
			const addCriteriaBtn = document.querySelector('#addCriteriaBtn');
			if (addCriteriaBtn) {
				addCriteriaBtn.addEventListener('click', function() {
					addCriteriaModal.show();
				});
			}

			// Edit button click handlers
			const editButtons = document.querySelectorAll('#criteriaTable .btn-success');
			editButtons.forEach(button => {
				button.addEventListener('click', function() {
					// Populate the edit form with data from button attributes
					document.getElementById('edit_criteria_id').value = this.getAttribute('data-id');
					document.getElementById('edit_criteria_name').value = this.getAttribute('data-name');
					document.getElementById('edit_contest').value = this.getAttribute('data-contest');
					document.getElementById('edit_criteria_description').value = this.getAttribute('data-description');
					document.getElementById('edit_max_score').value = this.getAttribute('data-max-score');
					
					// Show the modal
					editCriteriaModal.show();
				});
			});

			// Form submission handlers
			const addForm = document.getElementById('addCriteriaForm');
			if (addForm) {
				addForm.addEventListener('submit', function() {
					addCriteriaModal.hide();
				});
			}

			const editForm = document.getElementById('editCriteriaForm');
			if (editForm) {
				editForm.addEventListener('submit', function() {
					editCriteriaModal.hide();
				});
			}

			// Show success/error messages if they exist
			<?php if (isset($_SESSION['success'])): ?>
				Swal.fire({
					title: 'Success!',
					text: '<?php echo $_SESSION['success']; ?>',
					icon: 'success'
				});
				<?php unset($_SESSION['success']); ?>
			<?php endif; ?>

			<?php if (isset($_SESSION['error'])): ?>
				Swal.fire({
					title: 'Error!',
					text: '<?php echo $_SESSION['error']; ?>',
					icon: 'error'
				});
				<?php unset($_SESSION['error']); ?>
			<?php endif; ?>

			// Initialize Bootstrap modals for scores
			const addScoreModal = new bootstrap.Modal(document.getElementById('addScoreModal'));
			const editScoreModal = new bootstrap.Modal(document.getElementById('editScoreModal'));

			// Update max score display when criteria changes
			const criteriaSelect = document.getElementById('criteria_id');
			if (criteriaSelect) {
				criteriaSelect.addEventListener('change', function() {
					const selectedOption = this.options[this.selectedIndex];
					const maxScore = selectedOption.getAttribute('data-max-score');
					document.getElementById('maxScoreDisplay').textContent = maxScore;
					document.getElementById('score_value').max = maxScore;
				});
			}

			// Handle edit score button clicks
			const editScoreButtons = document.querySelectorAll('[data-bs-target="#editScoreModal"]');
			editScoreButtons.forEach(button => {
				button.addEventListener('click', function() {
					document.getElementById('edit_score_id').value = this.getAttribute('data-id');
					document.getElementById('edit_score_value').value = this.getAttribute('data-score');
					document.getElementById('edit_remarks').value = this.getAttribute('data-remarks');
					document.getElementById('editMaxScoreDisplay').textContent = this.getAttribute('data-max-score');
					document.getElementById('edit_score_value').max = this.getAttribute('data-max-score');
				});
			});

			// Form submission handlers
			const addScoreForm = document.getElementById('addScoreForm');
			if (addScoreForm) {
				addScoreForm.addEventListener('submit', function() {
					addScoreModal.hide();
				});
			}

			const editScoreForm = document.getElementById('editScoreForm');
			if (editScoreForm) {
				editScoreForm.addEventListener('submit', function() {
					editScoreModal.hide();
				});
			}
		});

		function showPage(id) {
			document.querySelectorAll('.content > div').forEach(div => div.classList.add('d-none'));
			document.getElementById(id).classList.remove('d-none');
			history.replaceState(null, null, '?page=' + id);
		}

		function searchTable(tableId, searchValue) {
			const input = searchValue.toLowerCase();
			const rows = document.querySelectorAll(`#${tableId} tbody tr`);
			rows.forEach(row => {
				const rowText = row.textContent.toLowerCase();
				row.style.display = rowText.includes(input) ? '' : 'none';
			});
		}

		function populateEditModal(element) {
			document.getElementById('edit_contestant_id').value = element.getAttribute('data-id');
			document.getElementById('edit_contestant_name').value = element.getAttribute('data-name');
			document.getElementById('edit_contestant_number').value = element.getAttribute('data-number');
			document.getElementById('edit_contest').value = element.getAttribute('data-contest');
			document.getElementById('edit_category').value = element.getAttribute('data-category');
			document.getElementById('edit_title').value = element.getAttribute('data-title');
			document.getElementById('edit_bio').value = element.getAttribute('data-bio');
			document.getElementById('edit_gender').value = element.getAttribute('data-gender');
		}

		function populateEditCategoryModal(element) {
			document.getElementById('edit_category_id').value = element.getAttribute('data-id');
			document.getElementById('edit_category_name').value = element.getAttribute('data-name');
			document.getElementById('edit_contest').value = element.getAttribute('data-contest');
			document.getElementById('edit_category_description').value = element.getAttribute('data-description');
		}

		function populateEditJudgeModal(element) {
			document.getElementById('edit_judge_id').value = element.getAttribute('data-id');
			document.getElementById('edit_judge_name').value = element.getAttribute('data-name');
			document.getElementById('edit_contact_information').value = element.getAttribute('data-info');
		}

		function confirmDeleteContestant(id) {
			Swal.fire({
				title: 'Are you sure?',
				text: "You won't be able to revert this!",
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#d33',
				cancelButtonColor: '#3085d6',
				confirmButtonText: 'Yes, delete it!'
			}).then((result) => {
				if (result.isConfirmed) {
					window.location.href = 'contestant_table_query.php?id=' + id;
				}
			});
		}

		function confirmDeleteCategory(id) {
			Swal.fire({
				title: 'Are you sure?',
				text: "You won't be able to revert this!",
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#d33',
				cancelButtonColor: '#3085d6',
				confirmButtonText: 'Yes, delete it!'
			}).then((result) => {
				if (result.isConfirmed) {
					window.location.href = 'category_table_query.php?id=' + id;
				}
			});
		}

		function confirmDeleteJudge(id) {
			Swal.fire({
				title: 'Are you sure?',
				text: "You won't be able to revert this!",
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#d33',
				cancelButtonColor: '#3085d6',
				confirmButtonText: 'Yes, delete it!'
			}).then((result) => {
				if (result.isConfirmed) {
					window.location.href = 'judge_table_query.php?id=' + id;
				}
			});
		}

		function confirmDeleteCriteria(id) {
			Swal.fire({
				title: 'Are you sure?',
				text: "You won't be able to revert this!",
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#d33',
				cancelButtonColor: '#3085d6',
				confirmButtonText: 'Yes, delete it!'
			}).then((result) => {
				if (result.isConfirmed) {
					window.location.href = 'criteria_table_query.php?id=' + id;
				}
			});
		}

		function confirmDeleteScore(id) {
			Swal.fire({
				title: 'Are you sure?',
				text: "You won't be able to revert this!",
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#d33',
				cancelButtonColor: '#3085d6',
				confirmButtonText: 'Yes, delete it!'
			}).then((result) => {
				if (result.isConfirmed) {
					window.location.href = 'score_table_query.php?id=' + id;
				}
			});
		}
	</script>
</body>

</html>