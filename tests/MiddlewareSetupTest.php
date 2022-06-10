<?php

namespace SocialSignIn\Test\ClientSuccessIntegration;

use PHPUnit\Framework\TestCase;
use Slim\App;

class MiddlewareSetupTest extends TestCase
{

    public function testMiddleware(): void
    {
        $app = new App();
        $container = $app->getContainer();

        require __DIR__ . '/../src/container.php';
        require __DIR__ . '/../src/middleware.php';

        $this->assertTrue(true);
    }
}
