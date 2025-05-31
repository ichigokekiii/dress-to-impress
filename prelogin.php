<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Role - Dress to Impress</title>
    <link rel="stylesheet" href="prelogin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Role Selection Container -->
    <div class="role-container">
        <h1>Select your Role</h1>
        
        <div class="role-grid">
            <a href="login.php" class="role-card" data-role="admin">
                <div class="role-content">
                    <div class="role-icon">👑</div>
                    <h3>Admin</h3>
                    <p>An admin manages users, permissions, view logs, and system settings</p>
                </div>
                <div class="hover-content">
                    <div class="hover-emoji">💻</div>
                    <div class="hover-text">Master of the System</div>
                </div>
            </a>
            
            <a href="login3.php" class="role-card" data-role="organizer">
                <div class="role-content">
                    <div class="role-icon">📋</div>
                    <h3>Organizer</h3>
                    <p>An organizer creates and manages events, contestants, and schedules</p>
                </div>
                <div class="hover-content">
                    <div class="hover-emoji">🎭</div>
                    <div class="hover-text">Creator of Events</div>
                </div>
            </a>
            
            <a href="login2.php" class="role-card" data-role="judge">
                <div class="role-content">
                    <div class="role-icon">⚖️</div>
                    <h3>Judge</h3>
                    <p>A judge scores contestants based on set criteria</p>
                </div>
                <div class="hover-content">
                    <div class="hover-emoji">🏆</div>
                    <div class="hover-text">Voter of Winners</div>
                </div>
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
