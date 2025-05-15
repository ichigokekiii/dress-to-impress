
<!DOCTYPE html>
<html>
<head>
	<title>Test</title>
</head>
<body>

<?php
session_start();
$username = $_SESSION['username'];

?>

<h1>WELCOME, <?php echo $username; ?></h1>

</body>
</html>