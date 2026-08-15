<?php

$server = "localhost:3310";
$username = "root";
$password = "YOUR_PASSWORD_HERE";
$dbname = "iv";

$conn = mysqli_connect($server, $username, $password, $dbname);

if (!$conn) {
    die("Error" . mysqli_connect_error());
}

?>