<!DOCTYPE html>
<html>
<html>
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
                <a href="categoryHome.php" onclick="goToPage('category')">CATEGORY</a>
                <a href="contestantHome.php" onclick="goToPage('contestants')">CONTESTANTS</a>
                <a href="resultHome.php" onclick="goToPage('results')">RESULTS</a>
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

    <!-- Your Page Content Goes Here -->
    <div class="page-content">
        <h1>Your Page Content</h1>
        <p>Replace this section with your actual page content.</p>
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
        }
    </script>


</body>
</html>