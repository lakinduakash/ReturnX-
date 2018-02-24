<?php
function getDBConnection()
{
    $servername = "localhost";
    $username = "root";
    $password = "root@123";
    $dbname = "returnx";
// Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    if (!$conn) {
        die("Connection failed" . mysqli_error($conn));
        $login = false;
    }
    return $conn;
}
