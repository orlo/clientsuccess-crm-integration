<?php

use Assert\Assertion;
use Psr\Container\ContainerInterface;
use Slim\App;
use SocialSignIn\ExampleCrmIntegration\Authentication\SignatureAuthentication;

/** @var $app App */
/** @var $container ContainerInterface */

Assertion::isInstanceOf($app, App::class);
Assertion::isInstanceOf($container, ContainerInterface::class);

$auth = new SignatureAuthentication($container->get('shared_secret'));

$app->get('/search', 'search_controller')->add($auth)->setName('search');
$app->get('/iframe', 'i_frame_controller')->add($auth)->setName('i-frame');
$app->post('/interaction', 'interaction_controller')->add($auth)->setName('interaction');
$app->post('/webhook', 'webhook_controller')->setName('webhook');
