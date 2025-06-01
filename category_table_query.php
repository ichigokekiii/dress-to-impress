<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "connection.php";

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['update'])) {
    $category_id = $conn->real_escape_string($_POST['category_id']);
    $category_name = $conn->real_escape_string($_POST['category_name']);
    $fk_category_contest = $conn->real_escape_string($_POST['fk_category_contest']);
    $category_description = $conn->real_escape_string($_POST['category_description']);

    $update_query = "UPDATE category_table SET
                     category_name = '$category_name',
                     fk_category_contest = '$fk_category_contest',
                     category_description = '$category_description'
                     WHERE category_id = '$category_id'";

    if ($conn->query($update_query)) {
        // Log the update
        $action = $conn->real_escape_string("Updated category '$category_name'");
        $log_query = "INSERT INTO logs_table (action, log_time) VALUES ('$action', NOW())";
        $conn->query($log_query);
        
        header("Location: " . $_SERVER['HTTP_REFERER'] . "&category_success=updated");
        exit();
    } else {
        header("Location: " . $_SERVER['HTTP_REFERER'] . "&error=updatefail");
        exit();
    }
}

if (isset($_GET['category_success'])) {
    $page_to_show = 'categories';
    
    if ($_GET['category_success'] == 'added') {
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    title: 'Success!',
                    text: 'Category added successfully!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            }
        </script>";
    } elseif ($_GET['category_success'] == 'updated') {
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    title: 'Updated!',
                    text: 'Category updated successfully!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            }
        </script>";
    } elseif ($_GET['category_success'] == 'deleted') {
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    title: 'Deleted!',
                    text: 'Category has been deleted.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            }
        </script>";
    }
} elseif (isset($_GET['category_error'])) {
    $page_to_show = 'categories';
    
    if ($_GET['category_error'] == 'duplicate') {
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    title: 'Error!',
                    text: 'The category already exists.',
                    icon: 'error',
                    confirmButtonText: 'Try Again'
                });
            }
        </script>";
    } elseif ($_GET['category_error'] == 'in_use') {
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    title: 'Cannot Delete Category',
                    text: 'This category is currently being used by one or more contestants. Please reassign or remove those contestants first.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        </script>";
    }
}

?>

<?php //INSERT

if (isset($_POST['save_category'])) {
    $category_name = $conn->real_escape_string($_POST['category_name']);
    $fk_category_contest = $conn->real_escape_string($_POST['fk_category_contest']);
    $category_description = $conn->real_escape_string($_POST['category_description']);

    $insert_query = "INSERT INTO category_table (category_name, fk_category_contest, category_description)
                     VALUES ('$category_name', '$fk_category_contest', '$category_description')";

    if ($conn->query($insert_query)) {
        // Log the insertion
        $action = $conn->real_escape_string("Added new category '$category_name'");
        $log_query = "INSERT INTO logs_table (action, log_time) VALUES ('$action', NOW())";
        $conn->query($log_query);
        
        header("Location: " . $_SERVER['HTTP_REFERER'] . "&category_success=added");
        exit();
    } else {
        header("Location: " . $_SERVER['HTTP_REFERER'] . "&category_error=insertfail");
        exit();
    }
}
?>

<?php ////DELETE

if (isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);

    // Check if category is being used by any contestants
    $check_query = "SELECT COUNT(*) as count FROM contestant_table WHERE fk_contestant_category = '$id'";
    $check_result = $conn->query($check_query);
    $row = $check_result->fetch_assoc();

    if ($row['count'] > 0) {
        header("Location: " . $_SERVER['HTTP_REFERER'] . "&category_error=in_use");
        exit();
    }

    // Get category info for logging before deletion
    $info_query = "SELECT category_name FROM category_table WHERE category_id = '$id'";
    $info_result = $conn->query($info_query);
    $category = $info_result->fetch_assoc();

    $query = "DELETE FROM category_table WHERE category_id = '$id'";
    $result = $conn->query($query);

    if ($result) {
        // Log the deletion
        $action = $conn->real_escape_string("Deleted category '" . $category['category_name'] . "'");
        $log_query = "INSERT INTO logs_table (action, log_time) VALUES ('$action', NOW())";
        $conn->query($log_query);
        
        header("Location: " . $_SERVER['HTTP_REFERER'] . "&category_success=deleted");
        exit();
    } else {
        header("Location: " . $_SERVER['HTTP_REFERER'] . "&error=deletefail");
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
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="category_table_query.php" method="POST">
                        <input type="hidden" id="edit_category_id" name="category_id">
                        <div class="row mb-3">
                            <div class="col">
                                <label for="edit_category_name" class="form-label">Category Name</label>
                                <input type="text" class="form-control" id="edit_category_name" name="category_name" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="edit_contest" class="form-label">Contest</label>
                                <select class="form-select" id="edit_contest" name="fk_category_contest" required>
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
                                <label for="edit_category_description" class="form-label">Description</label>
                                <textarea class="form-control" id="edit_category_description" name="category_description" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col text-end">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" name="update">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>