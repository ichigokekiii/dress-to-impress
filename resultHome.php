<?php
session_start();
require_once "connection.php";

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Fetch all contests from the database
$query = "SELECT * FROM contest_table ORDER BY contest_date ASC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Results - Dress to Impress</title>
	<link rel="stylesheet" href="navbar.css">
	<link rel="stylesheet" href="contestant.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<style>
		.welcome-text {
			font-size: 3.5rem;
			font-weight: bold;
			margin-bottom: 30px;
			opacity: 0;
			animation: fadeIn 2s ease-in-out forwards;
			position: relative;
			z-index: 2;
			color: white;
			text-align: center;
		}

		.welcome-text::before {
			content: '';
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			width: 120%;
			height: 120%;
			background: radial-gradient(ellipse, rgba(223, 129, 179, 0.3) 0%, transparent 70%);
			z-index: -1;
			border-radius: 50%;
			filter: blur(20px);
		}

		.event-container {
			max-width: 800px;
			margin: 40px auto;
			padding: 20px;
			text-align: center;
		}
	</style>
</head>
<body>
	<!-- Navbar -->
	<nav class="navbar">
		<div class="navbar-container">
			<!-- Logo -->
			<a class="navbar-brand" href="#" onclick="goToHome()">
				<img src="dresstoimpress.png" alt="Dress to Impress Logo" height="55">
			</a>

			<!-- Navigation Links -->
			<div class="navbar-links">
				<a href="userHome.php">HOME</a>
				<a href="eventsHome.php">EVENTS</a>
				<a href="resultHome.php">RESULTS</a>
				<a href="contestantHome.php">CONTESTANTS</a>
			</div>

			<!-- User Info -->
			<a href="#" class="user-info" onclick="goToUserProfile()">
				<div class="user-avatar">👤</div>
				<span id="username">
					<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; ?>
				</span>
			</a>
		</div>
	</nav>

	<!-- Event Selection Container -->
	<div class="event-container">
		<h1 class="welcome-text">Select Event to View Results</h1>
		
		<div class="event-list">
			<?php
			if ($result->num_rows > 0) {
				while ($contest = $result->fetch_assoc()) {
					$contestId = htmlspecialchars($contest['contest_id']);
					$contestName = htmlspecialchars($contest['contest_name']);
					$contestDate = date('F d, Y', strtotime($contest['contest_date']));
					$location = htmlspecialchars($contest['location']);
					
					echo "<a href='results1.php?id={$contestId}' class='event-button'>";
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
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>

	<script>
		// Add animation class to body after page load
		document.addEventListener('DOMContentLoaded', function() {
			document.body.classList.add('loaded');
			
			// Add staggered animation to event buttons
			const buttons = document.querySelectorAll('.event-button');
			buttons.forEach((button, index) => {
				button.style.animationDelay = `${0.2 + (index * 0.1)}s`;
			});
		});

		function goToHome() {
			window.location.href = 'userHome.php';
		}

		function goToUserProfile() {
			console.log('Going to user profile');
		}
	</script>
</body>
</html>