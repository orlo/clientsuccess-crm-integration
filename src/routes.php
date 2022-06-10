<?php

use Assert\Assertion;
use Slim\App;

/* @var App $app */

if (!isset($app)) {
    throw new \InvalidArgumentException("wrong context");
}

Assertion::isInstanceOf($app, App::class);

$app->get('/search', 'search_controller')->setName('search');
$app->get('/iframe', 'i_frame_controller')->setName('i-frame');
$app->post('/interaction', 'interaction_controller')->setName('interaction');
$app->post('/webhook', 'webhook_controller')->setName('webhook');
