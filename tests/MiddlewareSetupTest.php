<?php

namespace SocialSignIn\Test\ClientSuccessIntegration;

use Slim\App;

class MiddlewareSetupTest extends \PHPUnit_Framework_TestCase
{

    public function testMiddleware()
    {
        $app = new App();
        $container = $app->getContainer();

        require __DIR__ . '/../src/container.php';
        require __DIR__ . '/../src/middleware.php';

        $this->assertTrue(true);
    }
}
