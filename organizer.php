<?php
session_start();
require_once "connection.php";
include "contestant_table_query.php";
include "category_table_query.php";
include "judge_table_query.php";

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['userType'] !== 'Staff') {
	header("Location: login.php");
	exit();
}

// Handle Contest Operations
if (isset($_POST['save_contest'])) {
	$contest_name = $conn->real_escape_string($_POST['contest_name']);
	$contest_date = $conn->real_escape_string($_POST['contest_date']);
	$location = $conn->real_escape_string($_POST['location']);

	$query = "INSERT INTO contest_table (contest_name, contest_date, location) 
			  VALUES ('$contest_name', '$contest_date', '$location')";

	$response = array();
	if ($conn->query($query)) {
		$response['status'] = 'success';
		$response['message'] = 'Contest added successfully';
	} else {
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
			  SET contest_name = '$contest_name', contest_date = '$contest_date', location = '$location'
			  WHERE contest_id = $contest_id";

	$response = array();
	if ($conn->query($query)) {
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

// Get overview statistics
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
	<link rel="stylesheet" href="style.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
	<div class="sidebar">
		<h4 class="p-3">Organizer Dashboard</h4>
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
							<h5 class="modal-title" id="addContestantModalLabel">Add Category</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<form action="admin_dashboard.php" method="POST">
								<div class="row">
									<div class="col">
										<label for="category_name" class="form-label">Category Name</label>
										<input type="text" class="form-control" id="category_name" name="category_name" required>
									</div>
								</div>

								<div class="row mt-4">
									<div class="col text-end">
										<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
										<button type="submit" class="btn btn-primary" name="submit">Save Category</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<input type="text" class="form-control search-box" placeholder="Search Category..." onkeyup="searchTable('categoryTable', this.value)">

			<?php
			$query = "SELECT * FROM category_table";
			$query_run = $conn->query($query);
			?>
			<table class="table table-bordered" id="categoryTable">
				<thead>
					<th style="width: 10%;">Category Id</th>
					<th>Category Name</th>
					<th style="width: 15%;">Action</th>
				</thead>

				<?php

				if ($query_run) {
					while ($row = mysqli_fetch_array($query_run)) {
				?>
						<tbody>
							<tr>
								<td><?php echo $row['category_id']; ?></td>
								<td><?php echo $row['category_name']; ?></td>
								<td class="">
									<a href="#" class="btn btn-success"
										data-bs-toggle="modal"
										data-bs-target="#editCategoryModal"
										data-id="<?php echo $row['category_id']; ?>"
										data-name="<?php echo $row['category_name']; ?>"
										onclick="populateEditCategoryModal(this)">
										Edit
									</a>
									<a href="#" class="btn btn-danger" onclick="confirmDeleteCategory(<?php echo $row['category_id']; ?>)">Delete</a>
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
			<button class="btn btn-primary mb-2">Add Score</button>
			<input type="text" class="form-control search-box" placeholder="Search Scores..." onkeyup="searchTable('score-table', this.value)">
			<div class="table-container">
				<table class="table table-bordered" id="score-table">
					<thead>
						<tr>
							<th>Judge Name</th>
							<th>Contestant Name</th>
							<th>Score Value</th>
						</tr>
					</thead>
					<tbody>
						<!-- PHP JOINED rows go here -->
					</tbody>
				</table>
			</div>
		</div>

		<!-- Criteria -->
		<div id="criteria" class="d-none">
			<h2>Criteria Table</h2>
			<button class="btn btn-primary mb-2">Add Criteria</button>
			<input type="text" class="form-control search-box" placeholder="Search Criteria..." onkeyup="searchTable('criteria-table', this.value)">
			<div class="table-container">
				<table class="table table-bordered" id="criteria-table">
					<thead>
						<tr>
							<th>Criteria Name</th>
							<th>Weight</th>
						</tr>
					</thead>
					<tbody>
						<!-- PHP-rows go here -->
					</tbody>
				</table>
			</div>
		</div>

	<script>
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
		}
		function populateEditJudgeModal(element) {
			document.getElementById('edit_judge_id').value = element.getAttribute('data-id');
			document.getElementById('edit_judge_name').value = element.getAttribute('data-name');
			document.getElementById('edit_contact_information').value = element.getAttribute('data-info');
		}

		function populateEditContestModal(element) {
			document.getElementById('edit_contest_id').value = element.getAttribute('data-id');
			document.getElementById('edit_contest_name').value = element.getAttribute('data-name');
			document.getElementById('edit_contest_date').value = element.getAttribute('data-date');
			document.getElementById('edit_location').value = element.getAttribute('data-location');
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

		document.addEventListener('DOMContentLoaded', function() {
			// Check URL parameters for page to show
			const urlParams = new URLSearchParams(window.location.search);
			const pageToShow = urlParams.get('page');

			// If a page parameter exists, show that page
			if (pageToShow) {
				showPage(pageToShow);
			}
		});
	</script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>