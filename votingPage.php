<?php
session_start();
require_once "connection.php";

// Get contestant ID from URL
$contestant_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch contestant details
$contestant_query = "SELECT * FROM contestant_table WHERE contestant_id = ?";
$stmt = $conn->prepare($contestant_query);
$stmt->bind_param("i", $contestant_id);
$stmt->execute();
$contestant = $stmt->get_result()->fetch_assoc();

if (!$contestant) {
    header("Location: contestantHome.php");
    exit();
}

// Fetch criteria for this contest
$criteria_query = "SELECT * FROM criteria_table WHERE fk_criteria_contest = ? ORDER BY criteria_id";
$stmt = $conn->prepare($criteria_query);
$stmt->bind_param("i", $contestant['fk_contestant_contest']);
$stmt->execute();
$criteria_result = $stmt->get_result();
$criteria = $criteria_result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting Page - <?php echo htmlspecialchars($contestant['contestant_name']); ?></title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="voting.css">
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
        <div class="voting-container">
            <!-- Contestant Image -->
            <div class="image-section">
                <img src="<?php echo $contestant['voting_image'] ? 'uploads/' . htmlspecialchars($contestant['voting_image']) : 'https://placehold.co/800x1000/666666/FFFFFF/png?text=Contestant+Photo'; ?>" 
                     alt="Contestant" class="contestant-image">
            </div>

            <!-- Voting Content -->
            <div class="voting-content">
                <div class="contestant-header">
                    <h2><?php echo htmlspecialchars($contestant['contestant_name']); ?></h2>
                    <p>Contestant <?php echo htmlspecialchars($contestant['contestant_number']); ?></p>
                </div>

                <!-- Dynamic Rating Sections -->
                <?php foreach ($criteria as $criterion): ?>
                <div class="rating-section">
                    <div class="rating-label">
                        <span><?php echo htmlspecialchars($criterion['criteria_name']); ?></span>
                        <span class="rating-percentage" id="<?php echo 'criteria-' . $criterion['criteria_id']; ?>-percentage">0.00%</span>
                    </div>
                    <div class="star-rating" id="<?php echo 'criteria-' . $criterion['criteria_id']; ?>-stars">
                        <div class="star"></div>
                        <div class="star"></div>
                        <div class="star"></div>
                        <div class="star"></div>
                        <div class="star"></div>
                    </div>
                    <div class="slider-container">
                        <input type="range" 
                               min="0" 
                               max="<?php echo $criterion['max_score']; ?>" 
                               value="0" 
                               step="0.01" 
                               class="rating-slider" 
                               id="<?php echo 'criteria-' . $criterion['criteria_id']; ?>-slider"
                               data-criteria-id="<?php echo $criterion['criteria_id']; ?>"
                               data-criteria-name="<?php echo htmlspecialchars($criterion['criteria_name']); ?>">
                    </div>
                </div>
                <?php endforeach; ?>

                <button class="vote-button" onclick="showVoteConfirmation()">Vote</button>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal-overlay" id="modalOverlay"></div>
    <div class="vote-modal" id="voteModal">
        <h3>Please confirm your vote</h3>
        <div class="vote-stats">
            <?php foreach ($criteria as $criterion): ?>
            <div class="stat-item">
                <span><?php echo htmlspecialchars($criterion['criteria_name']); ?>:</span>
                <span id="modal-criteria-<?php echo $criterion['criteria_id']; ?>">0.00%</span>
            </div>
            <?php endforeach; ?>
            <div class="stat-item">
                <strong>Total Vote:</strong>
                <strong id="modal-overall">0.00%</strong>
            </div>
        </div>
        <button class="confirm-vote-btn" onclick="confirmVote()">Confirm Vote</button>
    </div>

    <!-- Rules Modal -->
    <div class="modal-overlay" id="rulesModalOverlay"></div>
    <div class="vote-modal rules-modal" id="rulesModal">
        <h3>Rules and Criteria</h3>
        <div class="rules-content">
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
        </div>
        <button class="confirm-vote-btn" onclick="acceptRules()">I understand</button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Show rules modal on page load
        window.addEventListener('DOMContentLoaded', (event) => {
            const rulesModal = document.getElementById('rulesModal');
            const rulesOverlay = document.getElementById('rulesModalOverlay');
            
            rulesOverlay.style.display = 'block';
            rulesModal.style.display = 'block';
            
            // Trigger animations
            setTimeout(() => {
                rulesOverlay.classList.add('active');
                rulesModal.classList.add('active');
            }, 10);
        });

        function acceptRules() {
            const rulesModal = document.getElementById('rulesModal');
            const rulesOverlay = document.getElementById('rulesModalOverlay');
            
            rulesModal.classList.remove('active');
            rulesOverlay.classList.remove('active');
            
            // Wait for animations to finish before hiding
            setTimeout(() => {
                rulesModal.style.display = 'none';
                rulesOverlay.style.display = 'none';
            }, 500);
        }

        // Initialize all sliders
        const sliders = document.querySelectorAll('.rating-slider');
        const modal = document.getElementById('voteModal');
        const modalOverlay = document.getElementById('modalOverlay');

        sliders.forEach(slider => {
            const criteriaId = slider.dataset.criteriaId;
            const percentage = document.getElementById(`criteria-${criteriaId}-percentage`);
            const stars = document.getElementById(`criteria-${criteriaId}-stars`).children;

            // Set initial values
            percentage.textContent = '0.00%';

            slider.addEventListener('input', function() {
                const value = parseFloat(this.value).toFixed(2);
                percentage.textContent = `${value}%`;
                
                // Update stars
                const starCount = Math.round(parseFloat(value) / 20); // 20% per star
                Array.from(stars).forEach((star, index) => {
                    star.classList.toggle('filled', index < starCount);
                });
            });
        });

        // Close modal when clicking outside
        modalOverlay.addEventListener('click', function(e) {
            if (e.target === modalOverlay) {
                modal.classList.remove('active');
                modalOverlay.classList.remove('active');
                
                // Wait for animations to finish before hiding
                setTimeout(() => {
                    modal.style.display = 'none';
                    modalOverlay.style.display = 'none';
                }, 500);
            }
        });

        function calculateOverall() {
            const values = Array.from(sliders).map(slider => 
                parseFloat(slider.value)
            );
            return values.reduce((a, b) => a + b, 0) / values.length;
        }

        function showVoteConfirmation() {
            sliders.forEach(slider => {
                const criteriaId = slider.dataset.criteriaId;
                const value = parseFloat(slider.value).toFixed(2);
                document.getElementById(`modal-criteria-${criteriaId}`).textContent = `${value}%`;
            });

            const overall = calculateOverall().toFixed(2);
            document.getElementById('modal-overall').textContent = `${overall}%`;

            modalOverlay.style.display = 'block';
            modal.style.display = 'block';
            
            // Trigger animations
            setTimeout(() => {
                modalOverlay.classList.add('active');
                modal.classList.add('active');
            }, 10);
        }

        function confirmVote() {
            const scores = Array.from(sliders).map(slider => ({
                criteriaId: slider.dataset.criteriaId,
                criteriaName: slider.dataset.criteriaName,
                score: parseFloat(slider.value)
            }));

            // Send scores to server
            fetch('save_vote.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    contestantId: <?php echo $contestant_id; ?>,
                    scores: scores
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'confirmVote.php';
                } else {
                    alert('Error saving vote: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving vote. Please try again.');
            });
        }

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
