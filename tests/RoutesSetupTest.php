<?php

namespace SocialSignIn\Test\ClientSuccessIntegration;

use PHPUnit\Framework\TestCase;
use Slim\App;
use Slim\Route;
use Slim\Router;

class RoutesSetupTest extends TestCase
{

    public function testRoutes(): void
    {
        $app = new App();

        require __DIR__ . '/../src/routes.php';

        /** @var Router $router */
        $router = $app->getContainer()->get('router');

        $this->assertInstanceOf(Route::class, $router->getNamedRoute('search'));
        $this->assertInstanceOf(Route::class, $router->getNamedRoute('i-frame'));
    }
}
