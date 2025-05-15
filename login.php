<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container bg-dark text-white p-5 w-75">
        <form action="login.php" method="post">
            <div class="row">
                <div class="col">
                    <label for="" class="form-label">username</label>
                    <input type="text" name="username" id="uname" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="" class="form-label">password</label>
                    <input type="password" name="password" id="pw" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="col text-center mt-3">
                    <input type="submit" class="btn btn-primary" name="submit" value="submit">
                </div>
            </div>
        </form>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>

<?php
include "connection.php";



if (isset($_POST['submit'])) {

    session_start();
    $username = $_POST['username'];
    $password = $_POST['password'];

    $_SESSION['username'] = $username;

    $sql = "select * from login where username='" . $username . "' AND password='" . $password . "'";

    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        
        $field = $result->fetch_assoc();

        $role = $field['userType'];

        if ($role == "user") {
            header("location: userHome.php");

        } else if ($role == "admin") {
            header("location: admin_dashboard.php");

        }
    }

    //$result = mysqli_query($conn, $sql);

    /*$row = mysqli_fetch_array($result);


    if ($row["userType"] == "user") {
        $_SESSION["username"] == $username;
        header("location: userHome.php");
        echo "user";
    }
    if ($row["userType"] == "admin") {
        $_SESSION["username"] = $username;
        header("location: admin_dashboard.php");
        echo "admin";
    }*/
}

?>