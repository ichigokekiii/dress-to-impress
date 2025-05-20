<?php

$servername = "localhost";
$username = "root";
$password = "1234";
$database = "user";


$conn = new mysqli($servername, $username, $password, $database);

// if ($conn->connect_error) {
//     echo "Connection failed";
// } else{
//     echo "Connection success";
// }
//tinggal ko muna ung conn-error kasi yun ang error dito sa mac

?>