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

<!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-container">
            <!-- Logo -->
            <a class="navbar-brand" href="#" onclick="goToHome()">
                <img src="dresstoimpress.png" alt="Dress to Impress Logo" height="55">
            </a>

            <!-- Navigation Links -->
            <div class="navbar-links">
                <a href="userHome.php" onclick="goToPage('home')">HOME</a>
                <a href="eventsHome.php" onclick="goToPage('events')">EVENTS</a>
                <a href="resultHome.php" onclick="goToPage('results')">RESULTS</a>
                <a href="contestants.php" onclick="goToPage('contestants')">CONTESTANTS</a>
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

    <!-- Main Content -->
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
        // Function to navigate to home page
        function goToHome() {
            // Add your home page URL here
            console.log('Going to home page');
            // window.location.href = 'index.html';
        }

        // Function to navigate to different pages
        function goToPage(page) {
            // Add your page URLs here
            console.log('Going to ' + page + ' page');
            // Example: window.location.href = page + '.html';
        }

        // Function to navigate to user profile
        function goToUserProfile() {
            // Add your user profile page URL here
            console.log('Going to user profile');
            // window.location.href = 'profile.html';
        }

        // Function to update username in all relevant places
        function updateUsername(newUsername) {
            // Update navbar username
            document.getElementById('username').textContent = newUsername;
            
            // Update username in menu greeting
            const greetingUsername = document.querySelector('.user-greeting strong');
            if (greetingUsername) {
                greetingUsername.textContent = newUsername;
            }

            // Update welcome message username if it exists
            const welcomeUsername = document.getElementById('welcomeUsername');
            if (welcomeUsername) {
                welcomeUsername.textContent = newUsername;
            }
        }

        // Example usage of updateUsername function:
        // updateUsername('Jeff'); // This would change both usernames to 'Jeff'

        /* 
        PHP INTEGRATION GUIDE:
        
        1. For the header username, replace the content of the span with id="username":
           <span id="username"><?php echo htmlspecialchars($username); ?></span>
        
        2. For the welcome message username, replace the content of the span with id="welcomeUsername":
           <span id="welcomeUsername"><?php echo htmlspecialchars($username); ?></span>
        
        3. To get username from database in PHP:
           $username = $_SESSION['username']; // if stored in session
           // OR
           $username = getUsernameFromDatabase($user_id); // your custom function
        
        4. Add your logo image by replacing the comment in the navbar-brand section:
           <img src="path/to/your/logo.png" alt="Logo" height="40">
        */

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
    </script>
</body>
</html>