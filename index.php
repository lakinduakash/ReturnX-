<?php

require 'vendor/autoload.php';
//require 'connection.php';



function getConnection() {
    $dbhost="localhost:8889";
    $dbuser="root";
    $dbpass="root";
    $dbname="returnx";
    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}


$app = new \Slim\Slim();

$app->view(new \JsonApiView());
$app->add(new \JsonApiMiddleware());

$app->get('/api', function() use ($app) {
    $app->render(200,array(
        'message' => 'Success',
    ));
});



$app->post('/api/users', function () use ($app) {
    //$con = getConnection();
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

    $json = $app->request->getBody();
    $data = json_decode($json); // parse the JSON into an assoc.
    $email = $data->{'email'};
    $pass = $data->{'password'};

    $sql1 = "INSERT INTO `Users` (`email`, `password`) VALUES ('$email', '$pass');";
    $sql2 = "SELECT id FROM Users WHERE email = '$email'";


    $result1 = mysqli_query($conn, $sql1) or die (mysqli_error($conn));
    $result2 = mysqli_query($conn, $sql2) or die (mysqli_error($conn));

    $row = mysqli_fetch_assoc($result2);





    $app->render(201,array(
        'email' => $data ->{'email'},
        'self' => "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'/'.$row['id'],
    ));


    //print $data->{'email'};

});

$app->run();


?>
