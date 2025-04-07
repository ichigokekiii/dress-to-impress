<?php
include "connection.php";
include "insert.php";
include "edit.php";

if (isset($_GET['success']) && $_GET['success'] == 'added') {
	echo "<script>
			window.onload = function() {
				Swal.fire({
					title: 'Success!',
					text: 'Contestant added successfully!',
					icon: 'success',
					confirmButtonText: 'OK'
				});
				showPage('contestants');
			}
		</script>";
} elseif (isset($_GET['error']) && $_GET['error'] == 'duplicate') {
	echo "<script>
			window.onload = function() {
				Swal.fire({
					title: 'Duplicate ID!',
					text: 'The contestant ID already exists.',
					icon: 'error',
					confirmButtonText: 'Try Again'
				});
				showPage('contestants');
			}
		</script>";
}
if (isset($_GET['success']) && $_GET['success'] == 'deleted') {
    echo "<script>
        window.onload = function() {
            Swal.fire({
                title: 'Deleted!',
                text: 'Contestant has been deleted.',
                icon: 'success',
                confirmButtonText: 'OK'
            });
            showPage('contestants');
        }
    </script>";
}

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
										<label for="contestant_id" class="form-label">Contestant Id</label>
										<input type="text" class="form-control" id="contestant_id" name="contestant_id" required>
									</div>
								</div>
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
										<button type="submit" class="btn btn-primary" name="submit">Save Contestant</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<input type="text" class="form-control search-box" placeholder="Search Contestants..." onkeyup="searchTable('contestantTable', this.value)">

			<?php
			$query = "SELECT * FROM contestant_table";
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
									<a href="#" class="btn btn-danger" onclick="confirmDelete(<?php echo $row['contestant_id']; ?>)">Delete</a>
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
			<h2>Category Table</h2>
			<button class="btn btn-primary mb-2">Add Category</button>
			<input type="text" class="form-control search-box" placeholder="Search Categories..." onkeyup="searchTable('category-table', this.value)">
			<div class="table-container">
				<table class="table table-bordered" id="category-table">
					<thead>
						<tr>
							<th>Category Name</th>
						</tr>
					</thead>
					<tbody>
						<!-- PHP-generated rows go here -->
					</tbody>
				</table>
			</div>
		</div>

		<!-- Judges -->
		<div id="judges" class="d-none">
			<h2>Judge Table</h2>
			<button class="btn btn-primary mb-2">Add Judge</button>
			<input type="text" class="form-control search-box" placeholder="Search Judges..." onkeyup="searchTable('judge-table', this.value)">
			<div class="table-container">
				<table class="table table-bordered" id="judge-table">
					<thead>
						<tr>
							<th>Name</th>
							<th>Contact Information</th>
						</tr>
					</thead>
					<tbody>
						<!-- PHP-generated rows go here -->
					</tbody>
				</table>
			</div>
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

		function confirmDelete(id) {
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
					window.location.href = 'delete.php?id=' + id;
				}
			});
		}
	</script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>