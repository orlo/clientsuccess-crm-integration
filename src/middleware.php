<?php

use Assert\Assertion;
use Psr\Container\ContainerInterface;
use Slim\App;
use SocialSignIn\ClientSuccessIntegration\Authentication\SignatureAuthentication;

/* @var App $app */
/* @var \Slim\Container $container */

if (!isset($app) || !isset($container)) {
    throw new \InvalidArgumentException("wrong context");
}
Assertion::isInstanceOf($app, App::class);
Assertion::isInstanceOf($container, ContainerInterface::class);

$signatureCheck = new SignatureAuthentication($container->get('shared_secret'), ['POST' => '/webhook']);

$signatureCheck->restrictParametersToThese([
    'message_social_network_type',
    'message_social_network_id',
    'message_author_social_network_id',
    'message_sentiment',
    'message_language_code',
    'message_socialsignin_id',
    'message_socialsignin_url',
    'message_author_socialsignin_id',
]);


$app->add($signatureCheck);
