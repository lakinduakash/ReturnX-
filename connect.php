<?php

$servername = "localhost:8889";
$username = "root";
$password = "root";
$dbname = "returnx";


// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed" . mysqli_error($conn));
    $login = false;
}
