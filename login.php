<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login - Dress to Impress</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="login.css">
</head>

<body>
    <div class="login-container">
        <div class="login-form">
            <h1>Welcome, Admin!</h1>
            <p>Please log in</p>
            
            <form action="login.php" method="post">
                <div class="form-group">
                    <input type="text" name="username" id="uname" placeholder="Username">
                </div>
                
                <div class="form-group">
                    <input type="password" name="password" id="pw" placeholder="Password">
                </div>
                
                <button type="submit" name="submit" class="login-btn">Login</button>
            </form>
        </div>
        
        <div class="image-section-admin"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
include "connection.php";

if (isset($_POST['submit'])) {
    session_start();
    $username = $_POST['username'];
    $password = $_POST['password'];

    // For debugging
    error_log("Login attempt - Username: " . $username);

    $sql = "SELECT * FROM users_table WHERE username='" . $username . "' AND password='" . $password . "'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Set all necessary session variables
        $_SESSION['user_id'] = $user['id']; // Make sure your table has an id column
        $_SESSION['username'] = $username;
        $_SESSION['userType'] = $user['userType'];
        
        error_log("User found - Type: " . $user['userType']); // For debugging
        
        if ($user['userType'] == "Admin") {
            error_log("Redirecting to admin dashboard"); // For debugging
            header("Location: admin_dashboard.php");
            exit(); // Important: Add exit after redirect
        } else if ($user['userType'] == "Judge") {
            header("Location: userHome.php");
            exit();
        } else if ($user['userType'] == "Staff") {
            header("Location: staff_dashboard.php");
            exit();
        }
    } else {
        error_log("Login failed - Invalid credentials or query error");
        // You might want to add an error message here
        $_SESSION['error'] = "Invalid username or password";
    }
}
?>