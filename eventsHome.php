<?php
session_start();
require_once "connection.php";

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Events - Dress to Impress</title>
	<!-- Google Fonts -->
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- Custom CSS -->
	<link rel="stylesheet" href="navbar.css">
	<link rel="stylesheet" href="events.css">
	<style>
		body {
			font-family: 'Poppins', sans-serif;
			background: linear-gradient(135deg, #2d2d2d 0%, #1a1a1a 100%);
			color: white;
			min-height: 100vh;
			margin: 0;
			padding: 0;
		}

		/* Ensure consistent navbar height */
		.navbar {
			height: 70px;
		}

		.navbar-container {
			height: 100%;
		}

		.navbar-brand img {
			height: 55px;
			transform: scale(1.3);
			transform-origin: left center;
		}

		.navbar-links {
			gap: 40px;
		}

		.navbar-links a {
			font-family: 'Poppins', sans-serif;
			font-weight: 500;
			font-size: 0.9rem;
			letter-spacing: 1px;
		}

		.user-info {
			min-width: 120px;
		}

		.page-content {
			max-width: 1400px;
			margin: 0 auto;
			padding: 40px 20px;
		}

		.page-content h1 {
			font-size: 2.5rem;
			font-weight: bold;
			text-align: center;
			margin-bottom: 30px;
			opacity: 0;
			animation: fadeIn 1.2s ease-out forwards;
		}

		@keyframes fadeIn {
			from {
				opacity: 0;
				transform: translateY(20px);
			}
			to {
				opacity: 1;
				transform: translateY(0);
			}
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

	<!-- Page Content -->
	<div class="page-content">
		<h1>Events</h1>
		<!-- Add your events content here -->
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>

	<script>
		function goToHome() {
			window.location.href = 'userHome.php';
		}

		function goToUserProfile() {
			console.log('Going to user profile');
		}

		function updateUsername(newUsername) {
			document.getElementById('username').textContent = newUsername;
		}
	</script>
</body>
</html>