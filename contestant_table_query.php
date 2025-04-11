<?php //EDIT
ob_start();
include "connection.php";

if (isset($_POST['update_contestant'])) {
    $contestant_id = $_POST['contestant_id'];
    $contestant_name = $_POST['contestant_name'];
    $contestant_number = $_POST['contestant_number'];
    $category = $_POST['category'];
    $description = $_POST['description'];

    $update_query_contestants = "UPDATE contestant_table SET
					 contestant_name = '$contestant_name',
					 contestant_number = '$contestant_number',
					 category = '$category',
					 descript = '$description'
					 WHERE contestant_id = '$contestant_id'";

    if (mysqli_query($conn, $update_query_contestants)) {
        header("Location: admin_dashboard.php?page=contestants&contestant_success=updated");
        exit();
    } else {
        header("Location: admin_dashboard.php?error=updatefail");
        exit();
    }
}

if (isset($_GET['contestant_success'])) {
    $page_to_show = 'contestants';
    
    if ($_GET['contestant_success'] == 'added') {
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    title: 'Success!',
                    text: 'Contestant added successfully!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            }
        </script>";
    } elseif ($_GET['contestant_success'] == 'updated') {
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    title: 'Updated!',
                    text: 'Contestant updated successfully!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            }
        </script>";
    } elseif ($_GET['contestant_success'] == 'deleted') {
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    title: 'Deleted!',
                    text: 'Contestant has been deleted.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            }
        </script>";
    }
} elseif (isset($_GET['contestant_error'])) {
    $page_to_show = 'contestants';
    
    if ($_GET['contestant_error'] == 'duplicate') {
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    title: 'Duplicate ID!',
                    text: 'The contestant ID already exists.',
                    icon: 'error',
                    confirmButtonText: 'Try Again'
                });
            }
        </script>";
    }
}
?>
<?php //INSERT

if (isset($_POST['save_contestant'])) {

    $contestant_name = $_POST['contestant_name'];
    $contestant_number = $_POST['contestant_number'];
    $category = $_POST['category'];
    $description = $_POST['description'];

    $insert_query_contestant = "INSERT INTO contestant_table (contestant_name, contestant_number, category, descript)
                     VALUES ('$contestant_name', '$contestant_number', '$category', '$description')";

    $result_contestant = mysqli_query($conn, $insert_query_contestant);

    if (!$result_contestant) {
        header("Location: admin_dashboard.php?contestant_error=insertfail");
        exit();
    } else {
        header("Location: admin_dashboard.php?page=contestants&contestant_success=added");
        exit();
    }
}
?>

<?php ////DELETE

if (isset($_GET['id'])) { 
	$id = $_GET['id'];

    $query_contestant = "DELETE FROM contestant_table WHERE contestant_id = '$id'";
    $result_contestant = mysqli_query($conn, $query_contestant); 

	if ($result_contestant) {
        header("Location: admin_dashboard.php?page=contestants&contestant_success=deleted");
		exit();
	} else {
		header("Location: admin_dashboard.php?error=deletefail");
		exit();
	}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div class="modal fade" id="editContestantModal" tabindex="-1" aria-labelledby="editContestantModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editContestantModalLabel">Edit Contestant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="admin_dashboard.php" method="POST">
                        <input type="hidden" id="edit_contestant_id" name="contestant_id">
                        <div class="row mb-3">
                            <div class="col">
                                <label for="edit_contestant_name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="edit_contestant_name" name="contestant_name" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label for="edit_contestant_number" class="form-label">Number</label>
                                <input type="text" class="form-control" id="edit_contestant_number" name="contestant_number" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label for="edit_category" class="form-label">Category</label>
                                <select class="form-select" id="edit_category" name="category" required>
                                    <option value="Miss">Miss</option>
                                    <option value="Mister">Mister</option>
                                    <option value="Teen">Teen</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col">
                                <label for="edit_description" class="form-label">Description</label>
                                <textarea class="form-control" id="edit_description" name="description" rows="4" required></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col text-end">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" name="update_contestant">Update</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

</body>

</html>
