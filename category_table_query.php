<?php //EDIT
include "connection.php";

if (isset($_POST['update'])) {
    $category_id = $_POST['category_id'];
    $category_name = $_POST['category_name'];

    $update_query = "UPDATE category_table SET
                     category_name = '$category_name'
                     WHERE category_id = '$category_id'";

    if ($conn->query($update_query)) {
        header("Location: admin_dashboard.php?page=categories&category_success=updated");
        exit();
    } else {
        header("Location: admin_dashboard.php?error=updatefail");
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
                    title: 'Duplicate ID!',
                    text: 'The category ID already exists.',
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

if (isset($_POST['submit'])) {
    

    $category_name = $_POST['category_name'];

    $check_query = "SELECT * FROM category_table WHERE category_id = '$category_id'"; // Check for duplicate ID
    $check_result = mysqli_query($conn, $check_query);

    if ($check_result->num_rows > 0) {
        header("Location: admin_dashboard.php?category_error=duplicate");
        exit();
    }

    $insert_query = "INSERT INTO category_table (category_name)
                     VALUES ('$category_name')";

    $result = $conn->query($insert_query);

    if (!$result) {
        header("Location: admin_dashboard.php?category_error=insertfail");
        exit();
    } else {
        header("Location: admin_dashboard.php?page=categories&category_success=added");
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
        header("Location: admin_dashboard.php?page=categories&category_error=in_use");
        exit();
    }

    $query = "DELETE FROM category_table WHERE category_id = '$id'";
    $result = $conn->query($query);

    if ($result) {
        header("Location: admin_dashboard.php?page=categories&category_success=deleted");
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
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryModalLabel">Edit </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="admin_dashboard.php" method="POST">
                        <input type="hidden" id="edit_category_id" name="category_id">
                        <div class="row mb-3">
                            <div class="col">
                                <label for="edit_category_name" class="form-label">Category Name</label>
                                <input type="text" class="form-control" id="edit_category_name" name="category_name" required>
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