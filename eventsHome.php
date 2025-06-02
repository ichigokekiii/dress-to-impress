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

		/* Event Container Styles */
		.event-container {
			max-width: 1400px;
			margin: 0 auto;
			padding: 40px 20px;
		}

		.event-container h1 {
			font-size: 3.5rem;
			font-weight: bold;
			text-align: center;
			margin-bottom: 40px;
			opacity: 0;
			animation: fadeIn 2s ease-in-out forwards;
			position: relative;
			z-index: 2;
			color: white;
		}

		.event-container h1::before {
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

		.event-list {
			display: flex;
			flex-direction: column;
			gap: 20px;
			max-width: 800px;
			margin: 0 auto;
		}

		.event-button {
			background: rgba(255, 255, 255, 0.1);
			border: none;
			padding: 20px;
			border-radius: 15px;
			color: white;
			text-decoration: none;
			display: flex;
			flex-direction: column;
			gap: 10px;
			transition: all 0.3s ease;
			opacity: 0;
			transform: translateY(20px);
			animation: slideUp 0.5s ease-out forwards;
			cursor: pointer;
			position: relative;
			overflow: hidden;
		}

		.event-button::before {
			content: '';
			position: absolute;
			top: 0;
			left: -100%;
			width: 100%;
			height: 100%;
			background: linear-gradient(
				90deg,
				transparent,
				rgba(223, 129, 179, 0.2),
				transparent
			);
			transition: 0.5s;
		}

		.event-button:hover::before {
			left: 100%;
		}

		.event-button:hover {
			background: rgba(223, 129, 179, 0.1);
			transform: translateY(-5px);
			color: white;
			box-shadow: 0 5px 20px rgba(223, 129, 179, 0.2);
		}

		.event-button strong {
			font-size: 1.4rem;
			font-weight: 600;
		}

		.event-date {
			color: #DF81B3;
			font-size: 1.1rem;
		}

		.event-location {
			font-size: 0.9rem;
			opacity: 0.8;
		}

		.event-location i {
			margin-right: 5px;
			color: #DF81B3;
		}

		/* Modal Styles */
		.modal-overlay {
			display: none;
			position: fixed;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background: rgba(0, 0, 0, 0);
			z-index: 1000;
			backdrop-filter: blur(0px);
			transition: all 0.3s ease;
		}

		.modal-overlay.active {
			background: rgba(0, 0, 0, 0.7);
			backdrop-filter: blur(5px);
		}

		.event-modal {
			display: none;
			position: fixed;
			top: 50%;
			left: 50%;
			transform: translate(-50%, calc(-50% + 50px));
			background: rgba(255, 255, 255, 0.95);
			padding: 40px;
			border-radius: 20px;
			z-index: 1100;
			color: #333;
			min-width: 400px;
			max-width: 90%;
			width: fit-content;
			text-align: center;
			box-shadow: 0 5px 30px rgba(0, 0, 0, 0.3);
			opacity: 0;
			transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
		}

		.event-modal.active {
			opacity: 1;
			transform: translate(-50%, -50%);
		}

		.event-modal h3 {
			color: #DF81B3;
			margin-bottom: 30px;
			font-size: 1.8rem;
			font-weight: 600;
			transform: translateY(-20px);
			opacity: 0;
			transition: all 0.5s ease 0.2s;
		}

		.event-modal.active h3 {
			transform: translateY(0);
			opacity: 1;
		}

		.event-details {
			margin-bottom: 30px;
			background: rgba(223, 129, 179, 0.1);
			padding: 20px;
			border-radius: 15px;
			transform: translateY(20px);
			opacity: 0;
			transition: all 0.5s ease 0.3s;
		}

		.event-modal.active .event-details {
			transform: translateY(0);
			opacity: 1;
		}

		.detail-item {
			display: grid;
			grid-template-columns: auto 1fr;
			gap: 20px;
			margin-bottom: 15px;
			font-size: 1.2rem;
			padding: 10px 0;
			border-bottom: 1px solid rgba(223, 129, 179, 0.2);
			opacity: 0;
			transition: all 0.3s ease;
			align-items: center;
		}

		.detail-item span:first-child {
			white-space: nowrap;
			color: #666;
		}

		.detail-item span:last-child {
			text-align: left;
			word-wrap: break-word;
			min-width: 200px;
		}

		.event-modal.active .detail-item {
			opacity: 1;
		}

		.event-modal.active .detail-item:nth-child(1) { transition-delay: 0.4s; }
		.event-modal.active .detail-item:nth-child(2) { transition-delay: 0.5s; }
		.event-modal.active .detail-item:nth-child(3) { transition-delay: 0.6s; }
		.event-modal.active .detail-item:nth-child(4) { transition-delay: 0.7s; }

		.detail-item:last-child {
			border-bottom: none;
			margin-bottom: 0;
		}

		.close-modal-btn {
			background: #DF81B3;
			color: white;
			border: none;
			padding: 12px 35px;
			border-radius: 8px;
			cursor: pointer;
			transition: all 0.3s ease;
			font-size: 1.1rem;
			font-weight: 600;
			text-transform: uppercase;
			letter-spacing: 1px;
			transform: translateY(20px);
			opacity: 0;
			transition: all 0.5s ease 0.9s;
		}

		.event-modal.active .close-modal-btn {
			transform: translateY(0);
			opacity: 1;
		}

		.close-modal-btn:hover {
			background: #c76a9a;
			transform: translateY(-2px);
			box-shadow: 0 4px 15px rgba(223, 129, 179, 0.4);
		}

		@keyframes fadeIn {
			from { opacity: 0; }
			to { opacity: 1; }
		}

		@keyframes slideUp {
			from {
				opacity: 0;
				transform: translateY(20px);
			}
			to {
				opacity: 1;
				transform: translateY(0);
			}
		}

		.no-events {
			text-align: center;
			padding: 40px;
			background: rgba(255, 255, 255, 0.1);
			border-radius: 15px;
			animation: fadeIn 1s ease forwards;
		}

		.no-events h3 {
			font-size: 1.8rem;
			margin-bottom: 15px;
			color: #DF81B3;
		}

		.no-events p {
			font-size: 1.1rem;
			opacity: 0.8;
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
				<a href="contestants.php">CONTESTANTS</a>
			</div>

			<!-- User Info -->
			<div class="user-info" onclick="toggleUserMenu(event)">
				<div class="user-avatar">👤</div>
				<span id="username">
					<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; ?>
				</span>
				<!-- User Menu Popout -->
				<div class="user-menu" id="userMenu">
					<div class="user-greeting">
						Hello, <strong><?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; ?></strong>
					</div>
					<a href="logout.php" class="user-menu-item">
						<i class="fas fa-sign-out-alt"></i>
						Logout
					</a>
				</div>
			</div>
		</div>
	</nav>

	<!-- Event Container -->
	<div class="event-container">
		<h1>Events</h1>
		
		<div class="event-list">
			<?php
			// Fetch all contests from the database
			$query = "SELECT 
						c.*, 
						(SELECT COUNT(*) FROM contestant_table WHERE fk_contestant_contest = c.contest_id) as contestant_count
					 FROM contest_table c 
					 ORDER BY c.contest_date ASC";
			$result = $conn->query($query);

			if ($result->num_rows > 0) {
				while ($contest = $result->fetch_assoc()) {
					$contestId = htmlspecialchars($contest['contest_id']);
					$contestName = htmlspecialchars($contest['contest_name']);
					$contestDate = date('F d, Y', strtotime($contest['contest_date']));
					$location = htmlspecialchars($contest['location']);
					$contestantCount = $contest['contestant_count'];
					
					echo "<div class='event-button' onclick='showEventDetails({$contestId}, \"{$contestName}\", \"{$contestDate}\", \"{$location}\", {$contestantCount})'>";
					echo "<strong>{$contestName}</strong>";
					echo "<span class='event-date'>{$contestDate}</span>";
					echo "<span class='event-location'><i class='fas fa-map-marker-alt'></i> {$location}</span>";
					echo "</div>";
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

	<!-- Modal -->
	<div class="modal-overlay" id="modalOverlay"></div>
	<div class="event-modal" id="eventModal">
		<h3 id="modalTitle">Event Details</h3>
		<div class="event-details" id="modalContent">
			<!-- Content will be dynamically inserted here -->
		</div>
		<button class="close-modal-btn" onclick="closeModal()">Close</button>
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

		function showEventDetails(contestId, name, date, location, contestantCount) {
			const modalContent = document.getElementById('modalContent');
			const modal = document.getElementById('eventModal');
			const overlay = document.getElementById('modalOverlay');
			
			modalContent.innerHTML = `
				<div class="detail-item">
					<span>Name:</span>
					<span>${name}</span>
				</div>
				<div class="detail-item">
					<span>Date:</span>
					<span>${date}</span>
				</div>
				<div class="detail-item">
					<span>Location:</span>
					<span>${location}</span>
				</div>
				<div class="detail-item">
					<span>Total Contestants:</span>
					<span>${contestantCount}</span>
				</div>
			`;
			
			overlay.style.display = 'block';
			modal.style.display = 'block';
			
			// Trigger animations
			setTimeout(() => {
				overlay.classList.add('active');
				modal.classList.add('active');
			}, 10);
		}

		function closeModal() {
			const modal = document.getElementById('eventModal');
			const overlay = document.getElementById('modalOverlay');
			
			modal.classList.remove('active');
			overlay.classList.remove('active');
			
			// Wait for animations to finish before hiding
			setTimeout(() => {
				modal.style.display = 'none';
				overlay.style.display = 'none';
			}, 500);
		}

		function goToHome() {
			window.location.href = 'userHome.php';
		}

		function toggleUserMenu(event) {
			event.stopPropagation();
			const menu = document.getElementById('userMenu');
			menu.classList.toggle('active');
		}

		// Close menu when clicking outside
		document.addEventListener('click', function(event) {
			const menu = document.getElementById('userMenu');
			const userInfo = document.querySelector('.user-info');
			if (!userInfo.contains(event.target)) {
				menu.classList.remove('active');
			}
		});

		// Prevent menu from closing when clicking inside it
		document.getElementById('userMenu').addEventListener('click', function(event) {
			event.stopPropagation();
		});

		// Function to update username in all relevant places
		function updateUsername(newUsername) {
			// Update navbar username
			document.getElementById('username').textContent = newUsername;
			
			// Update username in menu greeting
			const greetingUsername = document.querySelector('.user-greeting strong');
			if (greetingUsername) {
				greetingUsername.textContent = newUsername;
			}
		}
	</script>
</body>
</html>