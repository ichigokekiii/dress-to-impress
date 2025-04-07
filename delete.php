<?php
include "connection.php";


if (isset($_GET['id'])) { 
	$id = $_GET['id'];

	$query = "DELETE FROM contestant_table WHERE contestant_id = '$id'";
	$result = mysqli_query($conn, $query);

	if ($result) {
		header("Location: admin_dashboard.php?success=deleted");
		exit();
	} else {
		header("Location: admin_dashboard.php?error=deletefail");
		exit();
	}
}

?>
