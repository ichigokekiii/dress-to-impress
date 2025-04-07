<?php
include "connection.php";

if (isset($_POST['submit'])) {

    $contestant_id = $_POST['contestant_id'];
    $contestant_name = $_POST['contestant_name'];
    $contestant_number = $_POST['contestant_number'];
    $category = $_POST['category'];
    $description = $_POST['description'];

    $check_query = "SELECT * FROM contestant_table WHERE contestant_id = '$contestant_id'"; // Check for duplicate ID
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        header("Location: admin_dashboard.php?error=duplicate");
        exit();
    }

    $insert_query = "INSERT INTO contestant_table (contestant_id, contestant_name, contestant_number, category, descript)
                     VALUES ('$contestant_id', '$contestant_name', '$contestant_number', '$category', '$description')";

    $result = mysqli_query($conn, $insert_query);

    if (!$result) {
        header("Location: admin_dashboard.php?error=insertfail");
        exit();
    } else {
        header("Location: admin_dashboard.php?success=added");
        exit();
    }
}
?>
