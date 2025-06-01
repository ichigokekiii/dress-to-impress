<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Selection - Dress to Impress</title>
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="category1.css">
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
                <a href="userHome.php">HOME</a>
                <a href="eventsHome.php">EVENTS</a>
                <a href="resultHome.php">RESULTS</a>
                <a href="contestantHome.php">CONTESTANTS</a>
            </div>

            <!-- User Info -->
            <a href="#" class="user-info" onclick="goToUserProfile()">
                <div class="user-avatar">👤</div>
                <span id="username">Username</span>
            </a>
        </div>
    </nav>

    <!-- Contestant Selection Container -->
    <div class="contestant-container">
        <h1>Please select a Contestant</h1>
        
        <div class="contestant-grid">
            <?php
            // Generate 25 contestant cards
            for ($i = 1; $i <= 24; $i++) {
                echo '
                <a href="votingPage.php" class="contestant-card" onclick="selectContestant(' . $i . ')">
                    <div class="card-content">
                        <div class="default-view">
                            <img src="https://placehold.co/200x200/666666/FFFFFF/png?text=Contestant ' . $i . '" 
                                 alt="Contestant ' . $i . '" 
                                 class="contestant-img">
                        </div>
                        <div class="expanded-view">
                            <img src="https://placehold.co/400x300/DF81B3/FFFFFF/png?text=Contestant ' . $i . ' Expanded" 
                                 alt="Contestant ' . $i . ' Expanded" 
                                 class="contestant-img-expanded">
                            <div class="contestant-info">
                                <h3>Contestant #' . $i . '</h3>
                                <p>Contestant Name ' . $i . '</p>
                            </div>
                        </div>
                    </div>
                </a>';
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
