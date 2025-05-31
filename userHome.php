
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>User Page</title>
	<link rel="stylesheet" href="user.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<body>

<!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-container">
            <!-- Logo -->
            <a class="navbar-brand" href="#" onclick="goToHome()">
                <!-- REPLACE THIS COMMENT WITH YOUR LOGO IMAGE: <img src="your-logo.png" alt="Logo" height="35"> -->
                Your Logo
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
                <span id="username">
                    <!-- CONNECT TO DATABASE: Replace this with PHP code to get username from database -->
                    Username
                </span>
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <h1 class="welcome-text">
                Welcome, <span id="welcomeUsername">

				<?php
				/*session_start();
				$username = $_SESSION['username'];
				echo $username;*/
				?>	

                Username

				</span>
			</h1>

		 <p class="subtitle">What would you like to do today?</p>
            
            <div class="action-cards">
                <a href="contestantHome.php" class="action-card" onclick="goToPage('vote')">
                    <div class="action-card-icon">📊</div>
                    <div class="action-card-text">Vote</div>
                </a>
                
                <a href="#" class="action-card" onclick="goToPage('view')">
                    <div class="action-card-icon">👁️</div>
                    <div class="action-card-text">View</div>
                </a>
                
                <a href="#" class="action-card" onclick="goToPage('select')">
                    <div class="action-card-icon">👤</div>
                    <div class="action-card-text">Select</div>
                </a>
                
                <a href="#" class="action-card" onclick="goToPage('tally')">
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

        // Optional: Add dynamic username update function
        function updateUsername(newUsername) {
            document.getElementById('username').textContent = newUsername;
            document.getElementById('welcomeUsername').textContent = newUsername;
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
    </script>
</body>
</html>