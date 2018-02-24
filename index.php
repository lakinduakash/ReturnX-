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
    $pass = null;

    if(!isset($data->{'password'}))
    {
        $pass=generatePassword();
    }
    else
    {
        $pass = $data->{'password'};

        if(!checkPass($pass))
        {
            $app->render(400,array(
                'message' => 'Password complexity requirement not met',
                'developerMessage' => 'User creation failed because password complexity requirement not met',
            ));

        }

    }


    $sql1 = "INSERT INTO `Users` (`email`, `password`) VALUES ('$email', '$pass');";
    $sql2 = "SELECT id FROM Users WHERE email = '$email'";
    $result2 = mysqli_query($conn, $sql2) or die (mysqli_error($conn));
    if(mysqli_num_rows($result2)==0)
    {
        $result1 = mysqli_query($conn, $sql1) or die (mysqli_error($conn));
        //$row = mysqli_fetch_assoc($result2);
        $sql3 = "SELECT id FROM Users WHERE email = '$email'";
        $result3 = mysqli_query($conn, $sql3) or die (mysqli_error($conn));
        $row1 = mysqli_fetch_assoc($result3);
        $app->render(201,array(
            'email' => $email,
            'self' => "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'/'.$row1['id'],
        ));
    }
    else
    {
        $app->render(409,array(
            'message' => "A user with email: ".$email." already exists",
            'developerMessage' => "User creation failed because the email: " .$email. " already exists",
        ));
    }
}
function random_str(
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
function generatePassword(){
    $keyspace1 = '0123456789';
    $keyspace2 = 'abcdefghijklmnopqrstuvwxyz';
    $keyspace3 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $keyspace4 = '~!@#$%^&*_-+=`|\(){}[].?';
    $str = ''.random_str(1, $keyspace1).random_str(1, $keyspace2).random_str(1, $keyspace3).random_str(1, $keyspace4).random_str(3);
    return $str;
}


    function checkPass($pass) {
        $uppercase = preg_match('@[A-Z]@', $pass);
        $lowercase = preg_match('@[a-z]@', $pass);
        $number    = preg_match('@[0-9]@', $pass);
        $character = preg_match('@[\W]@',  $pass);
        if(!$uppercase || !$lowercase || !$number || !$character || strlen($pass) < 6 || strlen($pass) > 8) {
            return true;
        } else {
            return false;
        }

    }



$app->get('/api', function() use ($app) {
    $app->render(200,array(
        'message' => 'Success',
    ));
});
$app->post('/api/users', function () use ($app) { insertUser($app); });
$app->run();
