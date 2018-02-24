<?php

require 'vendor/autoload.php';
//require 'connection.php';

$app = new \Slim\Slim();

$app->view(new \JsonApiView());
$app->add(new \JsonApiMiddleware());



function getConnection() {
    $dbhost="localhost:8889";
    $dbuser="root";
    $dbpass="root";
    $dbname="returnx";
    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}

function insertUser($app)
{
    //$con = getConnection();
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

    $json = $app->request->getBody();
    $data = json_decode($json); // parse the JSON into an assoc.

    if(!isset($data->{'email'})){
        $app->render(400,array(
        ));
    }

    $email = $data->{'email'};
    $pass = $data->{'password'};

    if(!isset($data->{'password'}))
    {
        $pass=generatePassword();
    }




    $sql1 = "INSERT INTO `Users` (`email`, `password`) VALUES ('$email', '$pass');";
    $sql2 = "SELECT id FROM Users WHERE email = '$email'";


    $result2 = mysqli_query($conn, $sql2) or die (mysqli_error($conn));


    if(mysqli_num_rows($result2)==0)
    {
        $result1 = mysqli_query($conn, $sql1) or die (mysqli_error($conn));
        $row = mysqli_fetch_assoc($result2);

        $app->render(201,array(
            'email' => $email,
            'self' => $row['id'],
        ));
    }
    else
    {
        $app->render(409,array(
            'message' => "A user with email:".$email." already exist.",
            'developerMessage' => "User creation failed because the email: {$email} already exists.",
        ));

    }
}

function generatePassword()
{
    $length=7,
$keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ~!@#$%^&*_-+=`|\(){}[].?'
) {
$str = '';
$max = mb_strlen($keyspace, '8bit') - 1;
if ($max < 1) {
throw new Exception('$keyspace must be at least two characters long');
}
for ($i = 0; $i < $length; ++$i) {
$str .= $keyspace[random_int(0, $max)];
}
return $str;
}


$app->get('/api', function() use ($app) {
    $app->render(200,array(
        'message' => 'Success',
    ));
});




$app->post('/api/users', function () use ($app) { insertUser($app); });




$app->run();




?>
