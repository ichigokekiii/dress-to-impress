<?php
session_start();
require_once "connection.php";

// Get contest ID from URL
$contest_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch contest details
$contest_query = "SELECT * FROM contest_table WHERE contest_id = ?";
$stmt = $conn->prepare($contest_query);
$stmt->bind_param("i", $contest_id);
$stmt->execute();
$contest = $stmt->get_result()->fetch_assoc();

if (!$contest) {
    header("Location: resultHome.php");
    exit();
}

// Fetch contestants with their total scores, grouped by category
$contestant_query = "
    SELECT 
        c.*,
        cat.category_name,
        cat.category_id,
        COALESCE(AVG(s.score_value), 0) as total_score,
        COUNT(DISTINCT s.fk_score_judge) as vote_count
    FROM contestant_table c
    LEFT JOIN score_table s ON c.contestant_id = s.fk_score_contestant
    LEFT JOIN category_table cat ON c.fk_contestant_category = cat.category_id
    WHERE c.fk_contestant_contest = ?
    GROUP BY c.contestant_id
    ORDER BY cat.category_id, total_score DESC";

$stmt = $conn->prepare($contestant_query);
$stmt->bind_param("i", $contest_id);
$stmt->execute();
$result = $stmt->get_result();

// Group contestants by category
$categories = [];
while ($contestant = $result->fetch_assoc()) {
    $categoryId = $contestant['category_id'];
    if (!isset($categories[$categoryId])) {
        $categories[$categoryId] = [
            'name' => $contestant['category_name'],
            'contestants' => []
        ];
    }
    $categories[$categoryId]['contestants'][] = $contestant;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results - <?php echo htmlspecialchars($contest['contest_name']); ?></title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="navbar.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #2d2d2d 0%, #1a1a1a 100%);
            color: white;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        /* Navbar specific styles */
        .navbar-links a {
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            font-size: 0.9rem;
            letter-spacing: 1px;
        }

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

        .user-info {
            min-width: 120px;
        }

        .results-container {
            padding: 40px 20px;
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
        }

        .results-title {
            font-size: 3.5rem;
            font-weight: bold;
            margin-bottom: 40px;
            opacity: 0;
            animation: fadeIn 2s ease-in-out forwards;
            position: relative;
            z-index: 2;
            color: white;
            text-align: center;
        }

        .results-title::before {
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

        .category-section {
            margin-bottom: 40px;
            position: relative;
            padding: 20px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 15px;
            border: 1px solid rgba(223, 129, 179, 0.1);
        }

        .category-title {
            font-size: 1.6rem;
            margin-bottom: 25px;
            color: white;
            opacity: 0;
            animation: fadeIn 0.5s ease-out forwards;
            font-weight: 600;
            letter-spacing: 1px;
            position: relative;
            display: inline-block;
            padding-bottom: 10px;
        }

        .category-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(to right, #DF81B3, transparent);
            border-radius: 2px;
        }

        .category-title span {
            color: #DF81B3;
            font-weight: 500;
            margin-right: 8px;
        }

        .contestants-scroll {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            padding: 20px 0;
            scroll-behavior: smooth;
            position: relative;
            margin-right: 50px; /* Reverted back to original */
        }

        .contestants-scroll::-webkit-scrollbar {
            height: 8px;
        }

        .contestants-scroll::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }

        .contestants-scroll::-webkit-scrollbar-thumb {
            background: #DF81B3;
            border-radius: 4px;
        }

        .contestant-card {
            flex: 0 0 200px;
            aspect-ratio: 1;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            overflow: hidden;
            position: relative;
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(20px);
            animation: slideUp 0.5s ease-out forwards;
            cursor: pointer;
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
            color: white;
            text-align: center;
        }

        .contestant-card:hover .contestant-overlay {
            opacity: 1;
        }

        .scroll-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2); /* Changed to gray */
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 10;
            color: white;
            font-size: 1.5rem;
            opacity: 0.8;
            right: 0;
        }

        .scroll-arrow:hover {
            background: #DF81B3; /* Pink on hover */
            opacity: 1;
        }

        .scroll-right {
            right: 10px;
        }

        .contestant-name {
            font-size: 1rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .contestant-score {
            font-size: 1.8rem;
            color: #DF81B3;
            margin-bottom: 10px;
        }

        .vote-count {
            font-size: 0.8rem;
            opacity: 0.8;
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

        .vote-modal {
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
            max-width: 500px;
            text-align: center;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.3);
            opacity: 0;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .vote-modal.active {
            opacity: 1;
            transform: translate(-50%, -50%);
        }

        .vote-modal h3 {
            color: #DF81B3;
            margin-bottom: 30px;
            font-size: 1.8rem;
            font-weight: 600;
            transform: translateY(-20px);
            opacity: 0;
            transition: all 0.5s ease 0.2s;
        }

        .vote-modal.active h3 {
            transform: translateY(0);
            opacity: 1;
        }

        .vote-stats {
            margin-bottom: 30px;
            background: rgba(223, 129, 179, 0.1);
            padding: 20px;
            border-radius: 15px;
            transform: translateY(20px);
            opacity: 0;
            transition: all 0.5s ease 0.3s;
        }

        .vote-modal.active .vote-stats {
            transform: translateY(0);
            opacity: 1;
        }

        .stat-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 1.2rem;
            padding: 10px 0;
            border-bottom: 1px solid rgba(223, 129, 179, 0.2);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .vote-modal.active .stat-item {
            opacity: 1;
        }

        .vote-modal.active .stat-item:nth-child(1) { transition-delay: 0.4s; }
        .vote-modal.active .stat-item:nth-child(2) { transition-delay: 0.5s; }
        .vote-modal.active .stat-item:nth-child(3) { transition-delay: 0.6s; }
        .vote-modal.active .stat-item:nth-child(4) { transition-delay: 0.7s; }
        .vote-modal.active .stat-item:nth-child(5) { transition-delay: 0.8s; }

        .stat-item:last-child {
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

        .vote-modal.active .close-modal-btn {
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

        .contestant-card:nth-child(1) { animation-delay: 0.1s; }
        .contestant-card:nth-child(2) { animation-delay: 0.2s; }
        .contestant-card:nth-child(3) { animation-delay: 0.3s; }
        .contestant-card:nth-child(4) { animation-delay: 0.4s; }
        .contestant-card:nth-child(5) { animation-delay: 0.5s; }
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

    <!-- Results Container -->
    <div class="results-container">
        <h1 class="results-title"><?php echo htmlspecialchars($contest['contest_name']); ?> Results</h1>
        
        <?php foreach ($categories as $categoryId => $category): ?>
        <div class="category-section">
            <h2 class="category-title"><span>Category:</span><?php echo htmlspecialchars($category['name']); ?></h2>
            <div class="contestants-scroll" id="contestantsScroll-<?php echo $categoryId; ?>">
                <?php foreach ($category['contestants'] as $contestant): ?>
                    <div class="contestant-card" onclick="showDetails(<?php echo $contestant['contestant_id']; ?>)">
                        <img src="<?php echo $contestant['voting_image'] ? 'uploads/' . htmlspecialchars($contestant['voting_image']) : 'https://placehold.co/200x200/666666/FFFFFF/png?text=Contestant+Photo'; ?>" 
                             alt="Contestant <?php echo $contestant['contestant_number']; ?>" 
                             class="contestant-image">
                        <div class="contestant-overlay">
                            <div class="contestant-name">
                                <?php echo htmlspecialchars($contestant['contestant_name']); ?>
                            </div>
                            <div class="contestant-score">
                                <?php echo number_format($contestant['total_score'], 2); ?>%
                            </div>
                            <div class="vote-count">
                                <?php echo $contestant['vote_count']; ?> votes
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="scroll-arrow scroll-right" onclick="scrollRight(<?php echo $categoryId; ?>)">
                <i class="fas fa-chevron-right"></i>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Modal -->
    <div class="modal-overlay" id="modalOverlay"></div>
    <div class="vote-modal" id="detailsModal">
        <h3>Vote Details</h3>
        <div class="vote-stats" id="modalContent">
            <!-- Content will be dynamically inserted here -->
        </div>
        <button class="close-modal-btn" onclick="closeModal()">Close</button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>

    <script>
        function scrollRight(categoryId) {
            const container = document.getElementById(`contestantsScroll-${categoryId}`);
            container.scrollBy({ left: 300, behavior: 'smooth' });
        }

        function showDetails(contestantId) {
            // Fetch vote details from the server
            fetch(`get_vote_details.php?contestant_id=${contestantId}`)
                .then(response => response.json())
                .then(data => {
                    const modalContent = document.getElementById('modalContent');
                    let html = '';
                    
                    // Add criteria scores
                    data.criteria.forEach(criterion => {
                        html += `
                            <div class="stat-item">
                                <span>${criterion.name}:</span>
                                <span>${criterion.average_score.toFixed(2)}% (${criterion.vote_count} votes)</span>
                            </div>
                        `;
                    });
                    
                    // Add total score
                    html += `
                        <div class="stat-item">
                            <strong>Total Score:</strong>
                            <strong>${data.total_score.toFixed(2)}% (${data.total_votes} votes)</strong>
                        </div>
                    `;
                    
                    modalContent.innerHTML = html;
                    
                    const modal = document.getElementById('detailsModal');
                    const overlay = document.getElementById('modalOverlay');
                    
                    overlay.style.display = 'block';
                    modal.style.display = 'block';
                    
                    // Trigger animations
                    setTimeout(() => {
                        overlay.classList.add('active');
                        modal.classList.add('active');
                    }, 10);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error fetching vote details. Please try again.');
                });
        }

        function closeModal() {
            const modal = document.getElementById('detailsModal');
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

        // Close modal when clicking outside
        document.getElementById('modalOverlay').addEventListener('click', closeModal);
    </script>
</body>
</html> 