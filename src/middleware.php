<?php

use Assert\Assertion;
use Psr\Container\ContainerInterface;
use Slim\App;

Assertion::isInstanceOf($app, App::class);
Assertion::isInstanceOf($container, ContainerInterface::class);
