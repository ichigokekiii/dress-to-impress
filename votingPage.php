<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting Page - Dress to Impress</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="voting.css">
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

    <!-- Main Content Container -->
    <div class="main-container">
        <div class="voting-container">
            <!-- Contestant Image -->
            <div class="image-section">
                <img src="https://placehold.co/800x1000/666666/FFFFFF/png?text=Contestant+Photo" alt="Contestant" class="contestant-image">
            </div>

            <!-- Voting Content -->
            <div class="voting-content">
                <div class="contestant-header">
                    <h2>Contestant Name</h2>
                    <p>Contestant Number</p>
                </div>

                <!-- Rating Sections -->
                <div class="rating-section">
                    <div class="rating-label">
                        <span>Appearance</span>
                        <span class="rating-percentage" id="appearance-percentage">0.00%</span>
                    </div>
                    <div class="star-rating" id="appearance-stars">
                        <div class="star"></div>
                        <div class="star"></div>
                        <div class="star"></div>
                        <div class="star"></div>
                        <div class="star"></div>
                    </div>
                    <div class="slider-container">
                        <input type="range" min="0" max="100" value="0" step="0.01" class="rating-slider" id="appearance-slider">
                    </div>
                </div>

                <div class="rating-section">
                    <div class="rating-label">
                        <span>Q&A</span>
                        <span class="rating-percentage" id="qa-percentage">0.00%</span>
                    </div>
                    <div class="star-rating" id="qa-stars">
                        <div class="star"></div>
                        <div class="star"></div>
                        <div class="star"></div>
                        <div class="star"></div>
                        <div class="star"></div>
                    </div>
                    <div class="slider-container">
                        <input type="range" min="0" max="100" value="0" step="0.01" class="rating-slider" id="qa-slider">
                    </div>
                </div>

                <div class="rating-section">
                    <div class="rating-label">
                        <span>Talent</span>
                        <span class="rating-percentage" id="talent-percentage">0.00%</span>
                    </div>
                    <div class="star-rating" id="talent-stars">
                        <div class="star"></div>
                        <div class="star"></div>
                        <div class="star"></div>
                        <div class="star"></div>
                        <div class="star"></div>
                    </div>
                    <div class="slider-container">
                        <input type="range" min="0" max="100" value="0" step="0.01" class="rating-slider" id="talent-slider">
                    </div>
                </div>

                <div class="rating-section">
                    <div class="rating-label">
                        <span>Overall Impact</span>
                        <span class="rating-percentage" id="impact-percentage">0.00%</span>
                    </div>
                    <div class="star-rating" id="impact-stars">
                        <div class="star"></div>
                        <div class="star"></div>
                        <div class="star"></div>
                        <div class="star"></div>
                        <div class="star"></div>
                    </div>
                    <div class="slider-container">
                        <input type="range" min="0" max="100" value="0" step="0.01" class="rating-slider" id="impact-slider">
                    </div>
                </div>

                <button class="vote-button" onclick="showVoteConfirmation()">Vote</button>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal-overlay" id="modalOverlay"></div>
    <div class="vote-modal" id="voteModal">
        <h3>Please confirm your vote</h3>
        <div class="vote-stats">
            <div class="stat-item">
                <span>Appearance:</span>
                <span id="modal-appearance">0.00%</span>
            </div>
            <div class="stat-item">
                <span>Q&A:</span>
                <span id="modal-qa">0.00%</span>
            </div>
            <div class="stat-item">
                <span>Talent:</span>
                <span id="modal-talent">0.00%</span>
            </div>
            <div class="stat-item">
                <span>Overall Impact:</span>
                <span id="modal-impact">0.00%</span>
            </div>
            <div class="stat-item">
                <strong>Total Vote:</strong>
                <strong id="modal-overall">0.00%</strong>
            </div>
        </div>
        <button class="confirm-vote-btn" onclick="confirmVote()">Confirm Vote</button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Initialize all sliders
        const categories = ['appearance', 'qa', 'talent', 'impact'];
        const modal = document.getElementById('voteModal');
        const modalOverlay = document.getElementById('modalOverlay');

        categories.forEach(category => {
            const slider = document.getElementById(`${category}-slider`);
            const percentage = document.getElementById(`${category}-percentage`);
            const stars = document.getElementById(`${category}-stars`).children;

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
            const values = categories.map(category => 
                parseFloat(document.getElementById(`${category}-slider`).value)
            );
            return values.reduce((a, b) => a + b, 0) / values.length;
        }

        function showVoteConfirmation() {
            categories.forEach(category => {
                const value = parseFloat(document.getElementById(`${category}-slider`).value).toFixed(2);
                document.getElementById(`modal-${category}`).textContent = `${value}%`;
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
            // Redirect
            window.location.href = 'confirmVote.php';
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
