<?php
require_once "connection.php";
include "contestant_table_query.php";
include "category_table_query.php";
include "judge_table_query.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin Dashboard</title>
	<link rel="stylesheet" href="style.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
	<div class="sidebar">
		<h4 class="p-3">Admin Dashboard</h4>
		<a href="#overview" onclick="showPage('overview')">Overview</a>
		<a href="#contestants" onclick="showPage('contestants')">Contestant Table</a>
		<a href="#categories" onclick="showPage('categories')">Category Table</a>
		<a href="#judges" onclick="showPage('judges')">Judge Table</a>
		<a href="#scores" onclick="showPage('scores')">Score Table</a>
		<a href="#criteria" onclick="showPage('criteria')">Criteria Table</a>
		<a href="#users" onclick="showPage('users')">Users</a>
		<a href="#logs" onclick="showPage('logs')">Logs</a>
	</div>

	<div class="content">
		<div id="overview">
			<h2>Overview</h2>
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
							<form action="admin_dashboard.php" method="POST">
								<div class="row">
									<div class="col">
										<label for="contestant_name" class="form-label">Contestant Name</label>
										<input type="text" class="form-control" id="contestant_name" name="contestant_name" required>
									</div>
								</div>
								<div class="row">
									<div class="col">
										<label for="contestant_number" class="form-label">Contestant Number</label>
										<input type="text" class="form-control" id="contestant_number" name="contestant_number" required>
									</div>
								</div>
								<div class="row">
									<div class="col">
										<label for="category" class="form-label">Category</label>
										<select class="form-select" id="category" name="category" required>
											<option value="Miss">Miss</option>
											<option value="Mister">Mister</option>
											<option value="Teen">Teen</option>
										</select>
									</div>
								</div>
								<div class="row">
									<div class="col">
										<label for="description" class="form-label">Description</label>
										<textarea class="form-control" id="description" name="description" rows="4" required></textarea>
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
			$query = "SELECT * FROM Constestant";
			$query_run = mysqli_query($conn, $query);
			?>
			<table class="table table-bordered" id="contestantTable">
				<thead>
					<th>ID</th>
					<th>Name</th>
					<th>Number</th>
					<th>Category</th>
					<th style="width: 35%;">Description</th>
					<th style="width: 15%;">Action</th>
				</thead>

				<?php

				if ($query_run) {
					while ($row = mysqli_fetch_array($query_run)) {
				?>
						<tbody>
							<tr>
								<td><?php echo $row['contestant_id']; ?></td>
								<td><?php echo $row['contestant_name']; ?></td>
								<td><?php echo $row['contestant_number']; ?></td>
								<td><?php echo $row['category']; ?></td>
								<td><?php echo $row['descript']; ?></td>
								<td class="">
									<a href="#" class="btn btn-success"
										data-bs-toggle="modal"
										data-bs-target="#editContestantModal"
										data-id="<?php echo $row['contestant_id']; ?>"
										data-name="<?php echo $row['contestant_name']; ?>"
										data-number="<?php echo $row['contestant_number']; ?>"
										data-category="<?php echo $row['category']; ?>"
										data-description="<?php echo $row['descript']; ?>"
										onclick="populateEditModal(this)">
										Edit
									</a>
									<a href="#" class="btn btn-danger" onclick="confirmDeleteContestant(<?php echo $row['contestant_id']; ?>)">Delete</a>
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
			$query = "SELECT * FROM Category";
			$query_run = mysqli_query($conn, $query);
			?>
			<table class="table table-bordered" id="categoryTable">
				<thead>
					<th>Category Id</th>
					<th>Category Name</th>
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
			$query = "SELECT * FROM Judge";
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
			document.getElementById('edit_category').value = element.getAttribute('data-category');
			document.getElementById('edit_description').value = element.getAttribute('data-description');
		}

		function populateEditCategoryModal(element) {
			document.getElementById('edit_category_id').value = element.getAttribute('data-id');
			document.getElementById('edit_category_name').value = element.getAttribute('data-name');
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