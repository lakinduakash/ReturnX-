<?php

require 'vendor/autoload.php';

$app = new \Slim\Slim();

$app->view(new \JsonApiView());
$app->add(new \JsonApiMiddleware());

$app->get('/api', function() use ($app) {
    $app->render(200,array(
            'msg' => 'Success',
        ));
});

$app->run();

?>
