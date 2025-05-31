<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Contestants - Dress to Impress</title>
	<link rel="stylesheet" href="contestant.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
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
				<span id="username">Username</span>
			</a>
		</div>
	</nav>

	<!-- Event Selection Container -->
	<div class="event-container">
		<h1>Please select an Event</h1>
		
		<a href="event1.php" class="event-button" onclick="goToEvent('roblox')">
			Roblox: A Night to Remember in Bloxburg
		</a>
		
		<a href="#" class="event-button" onclick="goToEvent('cics')">
			CICS: Puksaan sa tabi ng dean's office
		</a>
		
		<a href="#" class="event-button" onclick="goToEvent('miss-cainta')">
			Miss Cainta 2025
		</a>
		
		<a href="#" class="event-button" onclick="goToEvent('pageant')">
			Gabi ng lagim: Pageant for a Cause (Sponsored by: Meralco)
		</a>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

	<script>
		function goToHome() {
			console.log('Going to home page');
		}

		function goToPage(page) {
			console.log('Going to ' + page + ' page');
		}

		function goToUserProfile() {
			console.log('Going to user profile');
		}

		function goToEvent(eventType) {
			console.log('Going to ' + eventType + ' event');
		}

		function updateUsername(newUsername) {
			document.getElementById('username').textContent = newUsername;
		}
	</script>
</body>
</html>