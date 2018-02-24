<?php
/**
 * Created by IntelliJ IDEA.
 * User: lakinduakash
 * Date: 2018-02-24
 * Time: 17.17
 */

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