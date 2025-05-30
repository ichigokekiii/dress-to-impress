<?php
ob_start();
include "connection.php";

// UPDATE
if (isset($_POST['update_contestant'])) {
    $contestant_id = $conn->real_escape_string($_POST['contestant_id']);
    $contestant_name = $conn->real_escape_string($_POST['contestant_name']);
    $contestant_number = $conn->real_escape_string($_POST['contestant_number']);
    $fk_contestant_contest = $conn->real_escape_string($_POST['contest']);
    $fk_contestant_category = $conn->real_escape_string($_POST['category']);
    $title = $conn->real_escape_string($_POST['title']);
    $bio = $conn->real_escape_string($_POST['bio']);
    $gender = $conn->real_escape_string($_POST['gender']);

    $update_query = "UPDATE contestant_table SET 
        contestant_name = '$contestant_name',
        contestant_number = '$contestant_number',
        fk_contestant_contest = '$fk_contestant_contest',
        fk_contestant_category = '$fk_contestant_category',
        title = '$title',
        bio = '$bio',
        gender = '$gender'
        WHERE contestant_id = '$contestant_id'";

    if ($conn->query($update_query)) {
        // Log the update
        $action = $conn->real_escape_string("Updated contestant '$contestant_name' (#$contestant_number)");
        $log_query = "INSERT INTO logs_table (action, log_time) VALUES ('$action', NOW())";
        $conn->query($log_query);

        header("Location: admin_dashboard.php?page=contestants&contestant_success=updated");
        exit();
    } else {
        header("Location: admin_dashboard.php?error=updatefail");
        exit();
    }
}

// INSERT
if (isset($_POST['save_contestant'])) {
    $contestant_name = $conn->real_escape_string($_POST['contestant_name']);
    $contestant_number = $conn->real_escape_string($_POST['contestant_number']);
    $fk_contestant_contest = $conn->real_escape_string($_POST['contest']);
    $fk_contestant_category = $conn->real_escape_string($_POST['category']);
    $title = $conn->real_escape_string($_POST['title']);
    $bio = $conn->real_escape_string($_POST['bio']);
    $gender = $conn->real_escape_string($_POST['gender']);

    // Check for duplicate contestant number in the same contest
    $check_query = "SELECT COUNT(*) as count FROM contestant_table 
                   WHERE contestant_number = '$contestant_number' 
                   AND fk_contestant_contest = '$fk_contestant_contest'";
    $result = $conn->query($check_query);
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        header("Location: admin_dashboard.php?page=contestants&contestant_error=duplicate");
        exit();
    }

    $insert_query = "INSERT INTO contestant_table 
        (contestant_name, contestant_number, fk_contestant_contest, fk_contestant_category, title, bio, gender) 
        VALUES 
        ('$contestant_name', '$contestant_number', '$fk_contestant_contest', '$fk_contestant_category', '$title', '$bio', '$gender')";

    if ($conn->query($insert_query)) {
        // Log the insertion
        $action = $conn->real_escape_string("Added contestant '$contestant_name' (#$contestant_number)");
        $log_query = "INSERT INTO logs_table (action, log_time) VALUES ('$action', NOW())";
        $conn->query($log_query);

        header("Location: admin_dashboard.php?page=contestants&contestant_success=added");
        exit();
    } else {
        header("Location: admin_dashboard.php?contestant_error=insertfail");
        exit();
    }
}

// DELETE
if (isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);

    // Get contestant info for logging before deletion
    $info_query = "SELECT contestant_name, contestant_number FROM contestant_table WHERE contestant_id = '$id'";
    $info_result = $conn->query($info_query);
    $contestant = $info_result->fetch_assoc();
    
    $delete_query = "DELETE FROM contestant_table WHERE contestant_id = '$id'";

    if ($conn->query($delete_query)) {
        // Log the deletion
        $action = $conn->real_escape_string("Deleted contestant '" . $contestant['contestant_name'] . "' (#" . $contestant['contestant_number'] . ")");
        $log_query = "INSERT INTO logs_table (action, log_time) VALUES ('$action', NOW())";
        $conn->query($log_query);

        header("Location: admin_dashboard.php?page=contestants&contestant_success=deleted");
        exit();
    } else {
        header("Location: admin_dashboard.php?error=deletefail");
        exit();
    }
}

// Success and error messages
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
                    title: 'Duplicate Number!',
                    text: 'The contestant number already exists for this contest.',
                    icon: 'error',
                    confirmButtonText: 'Try Again'
                });
            }
        </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Contestant</title>
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
                    <form action="contestant_table_query.php" method="POST">
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
                                <input type="number" class="form-control" id="edit_contestant_number" name="contestant_number" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label for="edit_contest" class="form-label">Contest</label>
                                <select class="form-select" id="edit_contest" name="contest" required>
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
                                <label for="edit_category" class="form-label">Category</label>
                                <select class="form-select" id="edit_category" name="category" required>
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
                                <label for="edit_title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="edit_title" name="title" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label for="edit_bio" class="form-label">Bio</label>
                                <textarea class="form-control" id="edit_bio" name="bio" rows="4" required></textarea>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label for="edit_gender" class="form-label">Gender</label>
                                <select class="form-select" id="edit_gender" name="gender" required>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
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