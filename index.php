<?php
require 'vendor/autoload.php';

include 'connect.php';
//require 'connection.php';
$app = new \Slim\Slim();
$app->view(new \JsonApiView());
$app->add(new \JsonApiMiddleware());

function insertDept($app){
    $conn = getDBConnection();

    $json = $app->request->getBody();
    $data = json_decode($json); // parse the JSON into an assoc.

    $name = $data->{'name'};
    $fid = $data->{'faculty_id'};


    $sql5 = "SELECT * FROM faculty WHERE id = '$fid'";

    $sql1 = "INSERT INTO `department` (`dname`,`fid`) VALUES ('$name','$fid');";
            $sql2 = "SELECT id FROM department WHERE dname = '$name'";

            $result5 = mysqli_query($conn, $sql5) or die (mysqli_error($conn));



            if (mysqli_num_rows($result5) != 0) {
                $result1 = mysqli_query($conn, $sql2);
                if (mysqli_num_rows($result1) == 0) {
                    
                    $result3 = mysqli_query($conn, $sql1) or die (mysqli_error($conn));
                    $row1 = mysqli_fetch_assoc($result1);
                    $app->render(201, array(
                    'name' => $name,
                    'self' => "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '/' . $row1['id'],
                    
                ));
                

                }
                else{
                    $app->render(409, array(

                    'message' => $row1['id'],
                    'developerMessage' => "",
                ));
                }
                //$row = mysqli_fetch_assoc($result2);
                //$sql3 = "SELECT id FROM department WHERE dname = '$name'";
                
                
            } else {
                $app->render(400, array(
                    'message' => "",
                    'developerMessage' => "",
                ));
            }




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
                    'self' => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '/' . $row1['id'],
                    
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
    $role=getRoleUser($app);
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
                    'role'=>$role,
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
            'role'=>$role,
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
function getRoleUser($app)
{
    $json = $app->request->getBody();
    $data = json_decode($json); // parse the JSON into an assoc.
    $user="user";
    $mod="moderator";
    $admin="admin";
    $role=null;
    if (isset($data->{'role'}))
    {
        $role=$data->{'role'};
        if($role==$user)
            return $user;
        else if($role==$mod)
            return $mod;
        else if($role==$admin)
            return $admin;
        else
        {
            $app->render(400, array(
            ));
        }
    }
    else
    {
        return $user;
    }
}
function insertUserMain()
{
}
function insertUserMobile()
{
}

function getDepartments($app)
{
    $conn = getDBConnection();
    $sql = "SELECT * FROM `Departments` ";
    $result = mysqli_query($conn, $sql);

    $row = array();
    //echo json_encode($result,JSON_FORCE_OBJECT);

    while ($r = mysqli_fetch_assoc($result)) {
        $row[] = $r;
    }

    header('Content-Type: application/json');
    echo '{"departments:"' . json_encode($row, JSON_UNESCAPED_SLASHES) . '}';
    exit();

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
$app->post('/api/departments', function () use ($app) {
    insertDept($app);
});

$app->get('/api/departments', function () use ($app) {
    getFaculties($app);
});
$app->run();
?>