<?php
session_start();
require_once "connection.php";

// Get category ID from URL
$category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch category details
$category_query = "SELECT * FROM category_table WHERE category_id = $category_id";
$category_result = $conn->query($category_query);
$category = $category_result->fetch_assoc();

// If category doesn't exist, redirect back
if (!$category) {
    header("Location: event1.php");
    exit();
}

// Fetch contestants for this category
$contestant_query = "SELECT * FROM contestant_table 
                    WHERE fk_contestant_category = $category_id 
                    ORDER BY contestant_number";
$contestants = $conn->query($contestant_query);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category['category_name']); ?> - Contestants</title>
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="category1.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Add animation styles */
        .contestant-card {
            opacity: 0;
            transform: translateY(50px);
            animation: slideUpFade 0.5s ease forwards;
        }

        @keyframes slideUpFade {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Add staggered delay to cards */
        <?php
        if ($contestants && $contestants->num_rows > 0) {
            $delay = 0;
            $contestants->data_seek(0);
            while ($contestant = $contestants->fetch_assoc()) {
                echo ".contestant-card:nth-child(" . $contestant['contestant_number'] . ") {
                    animation-delay: " . $delay . "s;
                }";
                $delay += 0.1;
            }
        }
        ?>

        .no-contestants {
            opacity: 0;
            animation: fadeIn 1s ease forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
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
                <img src="dresstoimpress.png" alt="Dress to Impress Logo" height="45">
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

    <!-- Contestant Selection Container -->
    <div class="contestant-container">
        <h1>Please select a Contestant</h1>
        
        <div class="contestant-grid">
            <?php
            if ($contestants && $contestants->num_rows > 0) {
                $contestants->data_seek(0);
                while ($contestant = $contestants->fetch_assoc()) {
                    // Default images if none are set
                    $profile_img = $contestant['profile_image'] ? 'uploads/' . $contestant['profile_image'] : 
                                 "https://placehold.co/200x200/666666/FFFFFF/png?text=Contestant " . $contestant['contestant_number'];
                    $expanded_img = $contestant['expanded_image'] ? 'uploads/' . $contestant['expanded_image'] : 
                                  "https://placehold.co/400x300/DF81B3/FFFFFF/png?text=Contestant " . $contestant['contestant_number'] . " Expanded";
                    
                echo '
                    <a href="votingPage.php?id=' . $contestant['contestant_id'] . '" class="contestant-card" onclick="selectContestant(' . $contestant['contestant_id'] . ')">
                    <div class="card-content">
                        <div class="default-view">
                                <img src="' . htmlspecialchars($profile_img) . '" 
                                     alt="Contestant ' . $contestant['contestant_number'] . '" 
                                 class="contestant-img">
                        </div>
                        <div class="expanded-view">
                                <img src="' . htmlspecialchars($expanded_img) . '" 
                                     alt="Contestant ' . $contestant['contestant_number'] . ' Expanded" 
                                 class="contestant-img-expanded">
                            <div class="contestant-info">
                                    <h3>Contestant #' . $contestant['contestant_number'] . '</h3>
                                    <p>' . htmlspecialchars($contestant['contestant_name']) . '</p>
                                    <p class="bio">' . htmlspecialchars($contestant['bio']) . '</p>
                            </div>
                        </div>
                    </div>
                </a>';
                }
            } else {
                echo '<div class="no-contestants text-white">
                        <h3>No Contestants Available</h3>
                        <p>No contestants have been added to this category yet.</p>
                      </div>';
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function selectContestant(contestantId) {
            console.log('Selected contestant:', contestantId);
            // Add your contestant selection logic here
        }

        function goToHome() {
            console.log('Going to home page');
            window.location.href = 'userHome.php';
        }

        function goToPage(page) {
            console.log('Going to ' + page + ' page');
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
