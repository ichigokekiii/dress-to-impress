<?php 
//EDIT
include "connection.php";

if (isset($_POST['update'])) {
    $category_id = $_POST['category_id'];
    $category_name = $_POST['category_name'];

    $update_query = "UPDATE Category SET
                     category_name = '$category_name'
                     WHERE category_id = '$category_id'";

    if (mysqli_query($conn, $update_query)) {
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
    }
}

?>

<?php 
//INSERT

if (isset($_POST['submit'])) {
    

    $category_name = $_POST['category_name'];

    $check_query = "SELECT * FROM Category WHERE category_id = '$category_id'"; // Check for duplicate ID
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        header("Location: admin_dashboard.php?category_error=duplicate");
        exit();
    }

    $insert_query = "INSERT INTO Category (category_name)
                     VALUES ('$category_name')";

    $result = mysqli_query($conn, $insert_query);

    if (!$result) {
        header("Location: admin_dashboard.php?category_error=insertfail");
        exit();
    } else {
        header("Location: admin_dashboard.php?page=categories&category_success=added");
        exit();
    }
}
?>

<?php 
//DELETE

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = "DELETE FROM Category WHERE category_id = '$id'";
    $result = mysqli_query($conn, $query);

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