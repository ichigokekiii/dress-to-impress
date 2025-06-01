<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Contestants - Dress to Impress</title>
	<link rel="stylesheet" href="navbar.css">
	<link rel="stylesheet" href="contestant.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
	<?php
	session_start();
	require_once "connection.php";

	// Fetch all contests from the database
	$query = "SELECT * FROM contest_table ORDER BY contest_date ASC";
	$result = $conn->query($query);
	?>

	<!-- Navbar -->
	<nav class="navbar">
		<div class="navbar-container">
			<!-- Logo -->
			<a class="navbar-brand" href="#" onclick="goToHome()">
				<img src="dresstoimpress.png" alt="Dress to Impress Logo" height="45">
			</a>

			<!-- Navigation Links -->
			<div class="navbar-links">
				<a href="userHome.php" onclick="goToPage('home')">HOME</a>
				<a href="eventsHome.php" onclick="goToPage('events')">EVENTS</a>
				<a href="resultHome.php" onclick="goToPage('results')">RESULTS</a>
				<a href="contestantHome.php" onclick="goToPage('contestants')">CONTESTANTS</a>
			</div>

			<!-- User Info -->
			<a href="#" class="user-info" onclick="goToUserProfile()">
				<div class="user-avatar">👤</div>
				<span id="username"><?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; ?></span>
			</a>
		</div>
	</nav>

	<!-- Event Selection Container -->
	<div class="event-container">
		<h1>Please select an Event</h1>
		
		<?php
		if ($result->num_rows > 0) {
			while ($contest = $result->fetch_assoc()) {
				$contestId = htmlspecialchars($contest['contest_id']);
				$contestName = htmlspecialchars($contest['contest_name']);
				$contestDate = date('F d, Y', strtotime($contest['contest_date']));
				$location = htmlspecialchars($contest['location']);
				
				echo "<a href='event1.php?id={$contestId}' class='event-button' onclick='goToEvent(\"{$contestId}\")'>";
				echo "<strong>{$contestName}</strong>";
				echo "<span class='event-date'>{$contestDate}</span>";
				echo "<span class='event-location'><i class='fas fa-map-marker-alt'></i> {$location}</span>";
				echo "</a>";
			}
		} else {
			echo "<div class='no-events'>";
			echo "<h3>No Events Available</h3>";
			echo "<p>Check back later for upcoming events!</p>";
			echo "</div>";
		}
		?>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>

	<script>
		function goToHome() {
			window.location.href = 'userHome.php';
		}

		function goToPage(page) {
			window.location.href = page + 'Home.php';
		}

		function goToUserProfile() {
			// Implement user profile navigation
			console.log('Going to user profile');
		}

		function goToEvent(contestId) {
			// Redirect to event1.php with the contest ID
			window.location.href = 'event1.php?id=' + contestId;
		}
	</script>
</body>
</html>