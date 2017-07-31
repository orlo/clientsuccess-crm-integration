<?php

use Assert\Assertion;
use Psr\Container\ContainerInterface;
use Slim\App;
use SocialSignIn\ExampleCrmIntegration\Controller\IFrameController;
use SocialSignIn\ExampleCrmIntegration\Controller\SearchController;
use SocialSignIn\ExampleCrmIntegration\Person\UserRepository;

Assertion::isInstanceOf($app, App::class);
Assertion::isInstanceOf($container, ContainerInterface::class);

$container['errorHandler'] = function ($c) {
    return function ($request, $response, $exception) use ($c) {
        error_log("Exception encountered " . get_class($exception) . ' / ' . $exception->getMessage());
        return $c['response']->withJson(['status' => 'error', 'error' => $exception->getMessage()], 500);
    };
};

$container['shared_secret'] = function () {
    $secret = getenv('SHARED_SECRET');
    if (empty($secret)) {
        throw new \InvalidArgumentException("SHARED_SECRET not defined in environment. Cannot continue.");
    }
    Assertion::notEmpty($secret);
    return $secret;
};

$container['cs_username'] = function () {
    $username = getenv('CS_USERNAME');
    if (empty($username)) {
        throw new \InvalidArgumentException("CS_USERNAME not defined in environment. Cannot continue.");
    }
    Assertion::notEmpty($username);
    return $username;
};

$container['cs_password'] = function () {
    $password = getenv('CS_PASSWORD');
    if (empty($password)) {
        throw new \InvalidArgumentException("CS_PASSWORD not defined in environment. Cannot continue.");
    }
    Assertion::notEmpty($password);
    return $password;
};

$container['person_repository'] = function (ContainerInterface $c) {
    return new UserRepository($c->get('cs_username'), $c->get('cs_password'));
};

$container['twig'] = function () {
    $loader = new Twig_Loader_Filesystem(__DIR__ . '/../templates');
    return new Twig_Environment($loader, []);
};

$container['search_controller'] = function (ContainerInterface $c) {
    return new SearchController($c->get('person_repository'));
};

$container['i_frame_controller'] = function (ContainerInterface $c) {
    return new IFrameController($c->get('twig'), $c->get('person_repository'));
};
