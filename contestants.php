<?php
session_start();
require_once "connection.php";

// Fetch all contestants with their contest information
$query = "SELECT 
            c.*, 
            ct.contest_name
          FROM contestant_table c
          LEFT JOIN contest_table ct ON c.fk_contestant_contest = ct.contest_id
          ORDER BY ct.contest_name, c.contestant_number";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contestants - Dress to Impress</title>
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

        /* Contestants Container */
        .contestants-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .contestants-container h1 {
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

        .contestants-container h1::before {
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

        .contestants-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .contestant-card {
            aspect-ratio: 1;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            overflow: hidden;
            position: relative;
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(20px);
            animation: slideUp 0.5s ease-out forwards;
        }

        .contestant-card:hover {
            transform: scale(1.1);
            z-index: 2;
            box-shadow: 0 10px 20px rgba(223, 129, 179, 0.3);
        }

        .contestant-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .contestant-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: all 0.3s ease;
            padding: 20px;
            text-align: center;
        }

        .contestant-card:hover .contestant-overlay {
            opacity: 1;
        }

        .contestant-name {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 10px;
            color: white;
        }

        .contest-name {
            font-size: 0.9rem;
            color: #DF81B3;
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

        .no-contestants {
            text-align: center;
            padding: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            animation: fadeIn 1s ease forwards;
            grid-column: 1 / -1;
        }

        .no-contestants h3 {
            font-size: 1.8rem;
            margin-bottom: 15px;
            color: #DF81B3;
        }

        .no-contestants p {
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

    <!-- Contestants Container -->
    <div class="contestants-container">
        <h1>Contestants</h1>
        
        <div class="contestants-grid">
            <?php
            if ($result->num_rows > 0) {
                while ($contestant = $result->fetch_assoc()) {
                    echo "<div class='contestant-card'>";
                    echo "<img src='" . ($contestant['voting_image'] ? 'uploads/' . htmlspecialchars($contestant['voting_image']) : 'https://placehold.co/400x400/666666/FFFFFF/png?text=Contestant+' . $contestant['contestant_number']) . "' 
                              alt='Contestant " . $contestant['contestant_number'] . "' 
                              class='contestant-image'>";
                    echo "<div class='contestant-overlay'>";
                    echo "<div class='contestant-name'>" . htmlspecialchars($contestant['contestant_name']) . "</div>";
                    echo "<div class='contest-name'>" . htmlspecialchars($contestant['contest_name']) . "</div>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<div class='no-contestants'>";
                echo "<h3>No Contestants Available</h3>";
                echo "<p>Check back later for contestants!</p>";
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
            
            // Add staggered animation to contestant cards
            const cards = document.querySelectorAll('.contestant-card');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${0.1 + (index * 0.05)}s`;
            });
        });

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