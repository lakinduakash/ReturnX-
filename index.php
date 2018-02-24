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



$uppercase = preg_match('@[A-Z]@', $pass);
$lowercase = preg_match('@[a-z]@', $pass);
$number    = preg_match('@[0-9]@', $pass);
$character = preg_match('@[\W]@',  $pass);

if(!$uppercase || !$lowercase || !$number || !$character || strlen($pass) < 6 || strlen($pass) > 8) {
   
       $app->render(400,array(
           'message' => 'Password complexity requirement not met',
           'developerMessage' => 'User creation failed because password complexity requirement not met',
       ));
 
}
  // $sql1 = "INSERT INTO `Users` (`email`, `password`) VALUES ('$email', '$pass');";
    // $sql2 = "SELECT id FROM Users WHERE email = '$email'";


    // $result1 = mysqli_query($conn, $sql1) or die (mysqli_error($conn));
    // $result2 = mysqli_query($conn, $sql2) or die (mysqli_error($conn));

    // $row = mysqli_fetch_assoc($result2);





    // $app->render(201,array(
        
    //     'self' => "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'/'.$row['id'],
    //     'email' => $data ->{'email'},
    // ));


    //print $data->{'email'};

});

$app->run();


?>