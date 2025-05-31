<?php
session_start();
require_once "connection.php";

// Check if user is already logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['userType'])) {
    // Redirect based on user type
    switch($_SESSION['userType']) {
        case 'Admin':
            header("Location: admin_dashboard.php");
            break;
        case 'Judge':
            header("Location: judge_scoring.php");
            break;
        case 'Staff':
            header("Location: organizer_dashboard.php");
            break;
    }
    exit();
}

$error = '';

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Using MD5 to match existing database pattern

    $stmt = $conn->prepare("SELECT users_id, username, userType FROM users_table WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Set session variables
        $_SESSION['user_id'] = $user['users_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['userType'] = $user['userType'];

        // Log the login action
        try {
            $log_stmt = $conn->prepare("INSERT INTO logs_table (user_id, action, log_time) VALUES (?, 'User logged in', NOW())");
            $log_stmt->bind_param("i", $user['users_id']);
            $log_stmt->execute();
        } catch (Exception $e) {
            // Log the error but continue with login
            error_log("Failed to log login action: " . $e->getMessage());
        }
        
        // Redirect based on user type
        switch($user['userType']) {
            case 'Admin':
                header("Location: admin_dashboard.php");
                break;
            case 'Judge':
                header("Location: judge_scoring.php");
                break;
            case 'Staff':
                header("Location: organizer_dashboard.php");
                break;
        }
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pageant Management System - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="login.css" rel="stylesheet">
  </head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-lg">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4">Pageant Management</h2>
                        
                        <?php if ($error): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                        <?php endif; ?>

        <form action="login.php" method="post">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" name="username" id="username" class="form-control" required>
                </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" id="password" class="form-control" required>
            </div>
                            <div class="d-grid">
                                <button type="submit" name="submit" class="btn btn-primary">Login</button>
                </div>
                        </form>
            </div>
                </div>
            </div>
        </div>
    </div>
       
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>


