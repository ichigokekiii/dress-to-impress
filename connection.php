<?php

<<<<<<< HEAD
$servername = "localhost";
$username = "root";
$password = "";
$database = "user";
=======
$servername = "localhost"; 
$username = "root"; 
$password = "1234"; 
$database = "pageant_db"; 
>>>>>>> remsmalopit


$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


?>