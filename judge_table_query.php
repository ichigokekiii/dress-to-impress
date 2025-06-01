<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ob_start();
include "connection.php";

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['update_judge'])) {
    $judge_id = $_POST['judge_id'];
    $judge_name = $_POST['judge_name'];
    $contact_information = $_POST['contact_information'];

    $update_query_judges = "UPDATE judge_table SET
					 judge_name = '$judge_name',
					 contact_information = '$contact_information',
					 WHERE judge_id = '$judge_id'";

    if ($conn->query($update_query_judges)) {
        header("Location: admin_dashboard.php?page=judges&judge_success=updated");
        exit();
    } else {
        header("Location: admin_dashboard.php?error=updatefail");
        exit();
    }
}

if (isset($_GET['judge_success'])) {
    $page_to_show = 'judges';

    if ($_GET['judge_success'] == 'added') {
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    title: 'Success!',
                    text: 'Judge added successfully!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            }
        </script>";
    } elseif ($_GET['judge_success'] == 'updated') {
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    title: 'Updated!',
                    text: 'Judge updated successfully!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            }
        </script>";
    } elseif ($_GET['judge_success'] == 'deleted') {
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    title: 'Deleted!',
                    text: 'Judge has been deleted.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            }
        </script>";
    }
}

?>
<?php //INSERT

if (isset($_POST['save_judge'])) {

    $judge_id = $_POST['judge_id'];
    $judge_name = $_POST['judge_name'];
    $contact_information = $_POST['contact_information'];

    $insert_query_judge = "INSERT INTO judge_table (judge_name, contact_information)
                     VALUES ('$judge_name', '$contact_information')";

    $result_judge = $conn->query($insert_query_judge);

    if ($result_judge) {
        header("Location: admin_dashboard.php?page=judges&judge_success=added");
        exit();
    } else {
        header("Location: admin_dashboard.php?judge_error=insertfail");
        exit();
    }
}
?>

<?php //DELETE

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query_judge = "DELETE FROM judge_table WHERE judge_id = '$id'";
    $result_judge = $conn->query($query_judge);

    if ($result_judge) {
        header("Location: admin_dashboard.php?page=judges&judge_success=deleted");
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
    <div class="modal fade" id="editJudgeModal" tabindex="-1" aria-labelledby="editJudgeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editJudgeModalLabel">Edit Judge</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="admin_dashboard.php" method="POST">
                        <input type="hidden" id="edit_judge_id" name="judge_id">
                        <div class="row mb-3">
                            <div class="col">
                                <label for="edit_judge_name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="edit_judge_name" name="judge_name" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="edit_judge_info" class="form-label">Contact Information</label>
                                <input type="text" class="form-control" id="edit_contact_information" name="contact_information" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col text-end">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" name="update_judge">Update</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

</body>

</html>