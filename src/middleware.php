<?php

use Assert\Assertion;
use Psr\Container\ContainerInterface;
use Slim\App;
use SocialSignIn\ExampleCrmIntegration\Authentication\SignatureAuthentication;

Assertion::isInstanceOf($app, App::class);
Assertion::isInstanceOf($container, ContainerInterface::class);

$app->add(new SignatureAuthentication($container->get('shared_secret'), ['POST' => '/webhook']));
