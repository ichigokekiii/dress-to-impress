<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "connection.php";

// Check if user is logged in and has appropriate access
if (!isset($_SESSION['username']) || !in_array($_SESSION['userType'], ['Admin', 'Staff'])) {
    header("Location: login.php");
    exit();
}

// Get user ID from the database if not in session
if (!isset($_SESSION['users_id'])) {
    $username = $conn->real_escape_string($_SESSION['username']);
    $query = "SELECT users_id FROM users_table WHERE username = '$username'";
    $result = $conn->query($query);
    if ($result && $row = $result->fetch_assoc()) {
        $_SESSION['users_id'] = $row['users_id'];
    }
}

if (isset($_POST['update_judge'])) {
    $judge_id = $_POST['judge_id'];
    $judge_name = $conn->real_escape_string($_POST['judge_name']);
    $contact_information = $conn->real_escape_string($_POST['contact_information']);

    try {
        // Start transaction
        $conn->begin_transaction();

        $update_query_judges = "UPDATE judge_table SET
                     judge_name = '$judge_name',
                     contact_information = '$contact_information'
                     WHERE judge_id = '$judge_id'";

        if ($conn->query($update_query_judges)) {
            // Only log if we have a valid user ID
            if (isset($_SESSION['users_id'])) {
                // Log the update action
                $log_query = "INSERT INTO logs_table (fk_logs_users, action, log_time) 
                             VALUES ('" . $_SESSION['users_id'] . "', 'Updated judge: $judge_name', NOW())";
                $conn->query($log_query);
            }
            
            // Commit transaction
            $conn->commit();
            
            $_SESSION['success'] = "Judge updated successfully!";
        } else {
            throw new Exception($conn->error);
        }
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $_SESSION['error'] = "Error updating judge: " . $e->getMessage();
    }

    if ($_SESSION['userType'] === 'Admin') {
        header("Location: admin_dashboard.php?page=judges");
    } else {
        header("Location: organizer.php?page=judges");
    }
    exit();
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
    $judge_id = intval($_GET['id']);
    
    // Check for related records in score_table
    $check_query = "SELECT COUNT(*) as count FROM score_table WHERE fk_score_judge = $judge_id";
    $check_result = $conn->query($check_query);
    $row = $check_result->fetch_assoc();
    
    if ($row['count'] > 0) {
        $_SESSION['error'] = "Cannot delete judge: There are scores associated with this judge";
        header("Location: admin_dashboard.php?page=judges");
        exit();
    }

    try {
        // Start transaction
        $conn->begin_transaction();

        // Delete the judge
        $query = "DELETE FROM judge_table WHERE judge_id = $judge_id";
        if ($conn->query($query)) {
            // Get the maximum ID after deletion
            $max_id_query = "SELECT MAX(judge_id) as max_id FROM judge_table";
            $result = $conn->query($max_id_query);
            $max_id = ($result->fetch_assoc())['max_id'] ?? 0;
            
            // Reset auto-increment to max_id + 1
            $reset_auto_increment = "ALTER TABLE judge_table AUTO_INCREMENT = " . ($max_id + 1);
            $conn->query($reset_auto_increment);

            // Commit transaction
            $conn->commit();
            
            $_SESSION['success'] = "Judge deleted successfully!";
        } else {
            throw new Exception($conn->error);
        }
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $_SESSION['error'] = "Error deleting judge: " . $e->getMessage();
    }
    
    header("Location: admin_dashboard.php?page=judges");
    exit();
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
                    <form action="judge_table_query.php" method="POST">
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