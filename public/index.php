<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = new \Slim\App(['settings' => ['displayErrorDetails' => false]]);

$container = $app->getContainer();

require __DIR__ . '/../src/container.php';
require __DIR__ . '/../src/middleware.php';
require __DIR__ . '/../src/routes.php';

$app->run();
