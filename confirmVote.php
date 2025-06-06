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
    <title>Vote Confirmation - Dress to Impress</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="confirmVote.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .navbar-links a {
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
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
            font-size: 0.9rem;
            letter-spacing: 1px;
        }
        .user-info {
            min-width: 120px;
        }
        .confirmation-content h1 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
        }
        .back-home-btn {
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
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

    <!-- Main Content Container -->
    <div class="main-container">
        <div class="confirmation-content">
            <h1>Your vote has been confirmed!</h1>
            <div class="success-animation">
                <div class="check-container">
                    <div class="check-background">
                        <svg viewBox="0 0 65 51" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7 25L27.3077 44L58.5 7" stroke="white" stroke-width="13" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                </div>
            </div>
            <button class="back-home-btn" onclick="goToHome()">Back to Home</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
