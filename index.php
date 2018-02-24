<?php
require 'vendor/autoload.php';
//require 'connection.php';
$app = new \Slim\Slim();
$app->view(new \JsonApiView());
$app->add(new \JsonApiMiddleware());


function getDBConnection()
{
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
    return $conn;
}



function insertFac($app){
    $conn = getDBConnection();

    $json = $app->request->getBody();
    $data = json_decode($json); // parse the JSON into an assoc.

    $name = $data->{'name'};

    $sql1 = "INSERT INTO `faculty` (`name`) VALUES ('$name');";
            $sql2 = "SELECT id FROM faculty WHERE name = '$name'";
            $result2 = mysqli_query($conn, $sql2) or die (mysqli_error($conn));
            if (mysqli_num_rows($result2) == 0) {
                $result1 = mysqli_query($conn, $sql1) or die (mysqli_error($conn));
                //$row = mysqli_fetch_assoc($result2);
                $sql3 = "SELECT id FROM faculty WHERE name = '$name'";
                $result3 = mysqli_query($conn, $sql3) or die (mysqli_error($conn));
                $row1 = mysqli_fetch_assoc($result3);
                $app->render(201, array(
                    'name' => $name,
                    'self' => "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '/' . $row1['id'],
                    
                ));
            } else {
                $app->render(409, array(
                    'message' => "A faculty with name: " . $name . " already exists.",
                    'developerMessage' => "Faculty creation failed because the faculty name: " . $name . " already exists.",
                ));
            }




}
function insertUser($app)
{
    $conn = getDBConnection();

    $json = $app->request->getBody();
    $data = json_decode($json); // parse the JSON into an assoc.


    if (!isset($data->{'email'})) {
        $app->render(400, array());
    }


    $email = $data->{'email'};
    $pass = null;
    $mobile=null;

    if (isset($data->{'mobile'}))
    {
        $mobile = $data->{'mobile'};
        if(validateMobile($mobile)) {
            $pass = generatePassword();
            

            $sql1 = "INSERT INTO `Users` (`email`, `password`,`mobile`) VALUES ('$email', '$pass','$mobile');";
            $sql2 = "SELECT id FROM Users WHERE email = '$email'";
            $result2 = mysqli_query($conn, $sql2) or die (mysqli_error($conn));
            if (mysqli_num_rows($result2) == 0) {
                $result1 = mysqli_query($conn, $sql1) or die (mysqli_error($conn));
                //$row = mysqli_fetch_assoc($result2);
                $sql3 = "SELECT id FROM Users WHERE email = '$email'";
                $result3 = mysqli_query($conn, $sql3) or die (mysqli_error($conn));
                $row1 = mysqli_fetch_assoc($result3);
                $app->render(201, array(
                    'email' => $email,
                    'self' => "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '/' . $row1['id'],
                    'mobile' => $mobile,
                ));
            } else {
                $app->render(409, array(
                    'message' => "A user with email: " . $email . " already exists",
                    'developerMessage' => "User creation failed because the email: " . $email . " already exists",
                ));
            }
        }
        else
        {
            $app->render(400, array(
            ));
        }

    }

    
    $sql2 = "SELECT id FROM Users WHERE email = '$email'";
    $result2 = mysqli_query($conn, $sql2) or die (mysqli_error($conn));
    if (mysqli_num_rows($result2) == 0) {


        if (!isset($data->{'password'})) {
        $pass = generatePassword();
    } else {
        $pass = $data->{'password'};

        if (!checkPass($pass)) {
            $app->render(400, array(
                'message' => 'Password complexity requirement not met',
                'developerMessage' => 'User creation failed because password complexity requirement not met',
            ));

        }

    }


    $sql1 = "INSERT INTO `Users` (`email`, `password`) VALUES ('$email', '$pass');";




        $result1 = mysqli_query($conn, $sql1) or die (mysqli_error($conn));
        //$row = mysqli_fetch_assoc($result2);
        $sql3 = "SELECT id FROM Users WHERE email = '$email'";
        $result3 = mysqli_query($conn, $sql3) or die (mysqli_error($conn));
        $row1 = mysqli_fetch_assoc($result3);
        $app->render(201, array(
            'email' => $email,
            'self' => "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '/' . $row1['id'],
        ));
    } else {
        $app->render(409, array(
            'message' => "A user with email: " . $email . " already exists",
            'developerMessage' => "User creation failed because the email: " . $email . " already exists",
        ));
    }
}

function random_str(
    $length = 7,
    $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ~!@#$%^&*_-+=`|\(){}[].?'
)
{
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

function generatePassword()
{
    $keyspace1 = '0123456789';
    $keyspace2 = 'abcdefghijklmnopqrstuvwxyz';
    $keyspace3 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $keyspace4 = '~!@#$%^&*_-+=`|\(){}[].?';
    $str = '' . random_str(1, $keyspace1) . random_str(1, $keyspace2) . random_str(1, $keyspace3) . random_str(1, $keyspace4) . random_str(3);
    return $str;
}


function checkPass($pass)
{
    $uppercase = preg_match('@[A-Z]@', $pass);
    $lowercase = preg_match('@[a-z]@', $pass);
    $number = preg_match('@[0-9]@', $pass);
    $character = preg_match('@[\W]@', $pass);
    if (!$uppercase || !$lowercase || !$number || !$character || strlen($pass) < 6 || strlen($pass) > 8) {
        return false;
    } else {
        return true;
    }

}

function validateMobile($mobile)
{
    return preg_match('/^[0-9]{11}+$/', intval($mobile));

}


$app->get('/api', function () use ($app) {
    $app->render(200, array(
        'message' => 'Success',
    ));
});
$app->post('/api/users', function () use ($app) {
    insertUser($app);
});
$app->post('/api/faculties', function () use ($app) {
    insertFac($app);
});

$app->get('/api/faculties', function () use ($app) {
    getFacs($app);
});

$app->run();
?>