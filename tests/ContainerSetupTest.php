<?php

namespace SocialSignIn\Test\ClientSuccessIntegration;

use PHPUnit\Framework\TestCase;
use Slim\App;
use SocialSignIn\ClientSuccessIntegration\Controller\IFrameController;
use SocialSignIn\ClientSuccessIntegration\Controller\SearchController;
use SocialSignIn\ClientSuccessIntegration\Person\RepositoryInterface;

class ContainerSetupTest extends TestCase
{

    public function testContainer(): void
    {
        $app = new App();
        $container = $app->getContainer();

        require __DIR__ . '/../src/container.php';

        $this->assertTrue($container->has('shared_secret'));
        $this->assertTrue($container->has('person_repository'));
        $this->assertTrue($container->has('twig'));
        $this->assertTrue($container->has('search_controller'));
        $this->assertTrue($container->has('i_frame_controller'));

        $this->assertTrue(is_string($container->get('shared_secret')));
        $this->assertInstanceOf(RepositoryInterface::class, $container->get('person_repository'));
        $this->assertInstanceOf(\Twig_Environment::class, $container->get('twig'));
        $this->assertInstanceOf(SearchController::class, $container->get('search_controller'));
        $this->assertInstanceOf(IFrameController::class, $container->get('i_frame_controller'));
    }
}
