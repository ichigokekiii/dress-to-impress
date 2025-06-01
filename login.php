<?php
include "connection.php";

if (isset($_POST['submit'])) {
    session_start();
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users_table WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION['username'] = $username;
        $_SESSION['userType'] = $user['userType'];
        
        if ($user['userType'] == "Admin") {
            header("Location: admin_dashboard.php");
        } else if ($user['userType'] == "Judge") {
            header("Location: userHome.php");
        } else if ($user['userType'] == "Staff") {
            header("Location: organizer.php");
        }
    }
}
?>

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