<?php

use Slim\Factory\AppFactory;

require_once __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();
    require_once __DIR__ . '/src/routes.php';
    //$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$app->run();
