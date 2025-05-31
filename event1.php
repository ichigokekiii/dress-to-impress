<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Categories - Dress to Impress</title>
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
            <a href="category1.php" class="category-card" data-theme="sunny" onclick="goToCategory('sunny')">
                <div class="category-content">
                    <div class="category-icon">☀️</div>
                    <span>Sunny</span>
                </div>
            </a>
            
            <a href="#" class="category-card" data-theme="beach" onclick="goToCategory('beach')">
                <div class="category-content">
                    <div class="category-icon">⛱️</div>
                    <span>Beach</span>
                </div>
            </a>
            
            <a href="#" class="category-card" data-theme="school" onclick="goToCategory('school')">
                <div class="category-content">
                    <div class="category-icon">🏫</div>
                    <span>School</span>
                </div>
            </a>
            
            <a href="#" class="category-card" data-theme="fancy" onclick="goToCategory('fancy')">
                <div class="category-content">
                    <div class="category-icon">🍷</div>
                    <span>Fancy</span>
                </div>
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let isAnimationComplete = false;
        let currentTheme = null;

        // Wait for initial animations to complete
        setTimeout(() => {
            isAnimationComplete = true;
            setupHoverEffects();
        }, 2500);

        function setupHoverEffects() {
            document.querySelectorAll('.category-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    if (!isAnimationComplete) return;
                    
                    const newTheme = this.getAttribute('data-theme');
                    if (currentTheme !== newTheme) {
                        applyTheme(newTheme);
                        requestAnimationFrame(() => {
                            document.body.style.setProperty('--theme-opacity', '1');
                        });
                    }
                });
            });

            // Handle mouse leaving all cards
            document.querySelector('.category-grid').addEventListener('mouseleave', function() {
                if (!isAnimationComplete || !currentTheme) return;
                
                document.body.style.setProperty('--theme-opacity', '0');
                
                // Wait for the opacity transition to complete before removing the theme
                document.body.addEventListener('transitionend', function handler(e) {
                    if (e.propertyName === 'opacity') {
                        currentTheme = null;
                        document.body.removeAttribute('data-theme');
                        document.body.removeEventListener('transitionend', handler);
                    }
                });
            });
        }

        function applyTheme(theme) {
            currentTheme = theme;
            document.body.setAttribute('data-theme', theme);
        }

        function goToCategory(category) {
            console.log('Going to ' + category + ' category');
            // Example: window.location.href = category + '.php';
        }

        function goToHome() {
            console.log('Going to home page');
            window.location.href = 'userHome.php';
        }
    </script>
</body>
</html>