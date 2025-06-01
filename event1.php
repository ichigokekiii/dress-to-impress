<?php
session_start();
require_once "connection.php";

// Get contest ID from URL
$contest_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch contest details
$query = "SELECT * FROM contest_table WHERE contest_id = $contest_id";
$result = $conn->query($query);
$contest = $result->fetch_assoc();

// If contest doesn't exist, redirect back to contestantHome.php
if (!$contest) {
    header("Location: contestantHome.php");
    exit();
}

// Fetch categories for this contest
$category_query = "SELECT * FROM category_table WHERE fk_category_contest = $contest_id";
$categories = $conn->query($category_query);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($contest['contest_name']); ?> - Categories</title>
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="event1.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a class="navbar-brand" href="#" onclick="goToHome()">
                <img src="dresstoimpress.png" alt="Dress to Impress Logo" height="55">
            </a>

            <div class="navbar-links">
                <a href="userHome.php">HOME</a>
                <a href="eventsHome.php">EVENTS</a>
                <a href="resultHome.php">RESULTS</a>
                <a href="contestantHome.php">CONTESTANTS</a>
            </div>

            <a href="#" class="user-info">
                <div class="user-avatar">👤</div>
                <span id="username">Username</span>
            </a>
        </div>
    </nav>

    <!-- Category Selection Container -->
    <div class="category-container">
        <h1>Please select a Category</h1>

        <div class="category-grid">
            <?php
            // Get categories for this specific contest
            $category_query = "SELECT * FROM category_table WHERE fk_category_contest = $contest_id";
            $category_result = $conn->query($category_query);

            if ($category_result && $category_result->num_rows > 0) {
                while ($category = $category_result->fetch_assoc()) {
                    echo "<a href='category1.php?id={$category['category_id']}' class='category-card'>";
                    echo "<div class='category-content'>";
                    echo "<div class='category-icon'><i class='fas fa-trophy'></i></div>";
                    echo "<span>" . htmlspecialchars($category['category_name']) . "</span>";
                    echo "</div>";
                    echo "</a>";
                }
            } else {
                echo "<div class='no-categories'>";
                echo "<h3>No Categories Available</h3>";
                echo "<p>Categories for this contest will be added soon!</p>";
                echo "</div>";
            }
            ?>
        </div>
    </div>

    <style>
        .category-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }

        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .category-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            text-decoration: none;
            color: white;
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .category-content {
            text-align: center;
        }

        .category-icon {
            font-size: 2em;
            margin-bottom: 10px;
        }

        .category-desc {
            display: block;
            margin-top: 8px;
            font-size: 0.8em;
            opacity: 0.8;
        }

        .no-categories {
            text-align: center;
            padding: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            grid-column: 1 / -1;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>

    <script>
        function generateRandomGradient() {
            const hue = Math.floor(Math.random() * 360);
            const saturation = Math.floor(Math.random() * 30) + 40;
            const lightness = Math.floor(Math.random() * 15) + 70;

            return `linear-gradient(to bottom, 
                hsla(${hue}, ${saturation}%, ${lightness}%, 0.4) 0%,
                hsla(${hue}, ${saturation}%, ${Math.max(lightness - 10, 60)}%, 0.4) 30%,
                hsla(${hue}, ${saturation}%, ${Math.max(lightness - 20, 50)}%, 0.4) 60%,
                hsla(${hue}, ${saturation}%, ${Math.max(lightness - 30, 40)}%, 0.4) 100%)`;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.category-card');

            // Handle animation completion for each card
            cards.forEach((card, index) => {
                // Calculate total delay for each card (animation delay + animation duration)
                const animationDelay = 0.3 + (index * 0.1); // Base delay (0.3s) plus stagger delay
                const animationDuration = 0.8; // Duration from CSS
                const totalDelay = (animationDelay + animationDuration) * 1000; // Convert to milliseconds

                // Enable hover effects after animation completes
                setTimeout(() => {
                    card.classList.add('animation-completed');
                }, totalDelay);

                card.addEventListener('mouseenter', function() {
                    if (this.classList.contains('animation-completed')) {
                        const randomGradient = generateRandomGradient();
                        document.documentElement.style.setProperty('--hover-gradient', randomGradient);
                        this.style.transform = 'translateY(-5px)';
                    }
                });

                card.addEventListener('mouseleave', function() {
                    if (this.classList.contains('animation-completed')) {
                        document.documentElement.style.setProperty('--hover-gradient', 'transparent');
                        this.style.transform = 'translateY(0)';
                    }
                });
            });
        });

        function goToCategory(categoryId) {
            window.location.href = 'category1.php?id=' + categoryId;
        }

        function goToHome() {
            window.location.href = 'userHome.php';
        }
    </script>
</body>

</html>