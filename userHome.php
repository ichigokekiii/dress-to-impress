<?php
session_start();
require_once "connection.php";

// Check if user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['userType'] !== 'Judge') {
	error_log("Access denied to admin dashboard - User: " . ($_SESSION['username'] ?? 'not set') . ", Type: " . ($_SESSION['userType'] ?? 'not set'));
	header("Location: login.php");
	exit();
}
    
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>User Page</title>
	<link rel="stylesheet" href="navbar.css">
	<link rel="stylesheet" href="user.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<body>

    <nav class="navbar">
        <div class="navbar-container">
            <a class="navbar-brand" href="#" onclick="goToHome()">
                <img src="dresstoimpress.png" alt="Dress to Impress Logo" height="55">
            </a>

            <div class="navbar-links">
                <a href="userHome.php" onclick="goToPage('home')">HOME</a>
                <a href="eventsHome.php" onclick="goToPage('events')">EVENTS</a>
                <a href="resultHome.php" onclick="goToPage('results')">RESULTS</a>
                <a href="contestants.php" onclick="goToPage('contestants')">CONTESTANTS</a>
            </div>

            <div class="user-info" onclick="toggleUserMenu(event)">
                <div class="user-avatar">👤</div>
                <span id="username">
                    <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; ?>
                </span>
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

    <div class="main-content">
        <div class="container">
            <h1 class="welcome-text">
                Welcome, <span id="welcomeUsername">
                    <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; ?>
                </span>
            </h1>

		 <p class="subtitle">What would you like to do today?</p>
            
            <div class="action-cards">
                <a href="contestantHome.php" class="action-card" onclick="goToPage('vote')">
                    <div class="action-card-icon">📊</div>
                    <div class="action-card-text">Vote</div>
                </a>
                
                <a href="eventsHome.php" class="action-card">
                    <div class="action-card-icon">👁️</div>
                    <div class="action-card-text">View</div>
                </a>
                
                <a href="contestants.php" class="action-card">
                    <div class="action-card-icon">👤</div>
                    <div class="action-card-text">Select</div>
                </a>
                
                <a href="resultHome.php" class="action-card">
                    <div class="action-card-icon">🏷️</div>
                    <div class="action-card-text">Tally</div>
                </a>
            </div>
        </div>
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

        function updateUsername(newUsername) {
            document.getElementById('username').textContent = newUsername;
            
            const greetingUsername = document.querySelector('.user-greeting strong');
            if (greetingUsername) {
                greetingUsername.textContent = newUsername;
            }

            const welcomeUsername = document.getElementById('welcomeUsername');
            if (welcomeUsername) {
                welcomeUsername.textContent = newUsername;
            }
        }

        function toggleUserMenu(event) {
            event.stopPropagation();
            const menu = document.getElementById('userMenu');
            menu.classList.toggle('active');
        }

        document.addEventListener('click', function(event) {
            const menu = document.getElementById('userMenu');
            const userInfo = document.querySelector('.user-info');
            if (!userInfo.contains(event.target)) {
                menu.classList.remove('active');
            }
        });

        document.getElementById('userMenu').addEventListener('click', function(event) {
            event.stopPropagation();
        });
    </script>
</body>
</html>