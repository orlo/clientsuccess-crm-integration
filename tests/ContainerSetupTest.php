<?php

namespace SocialSignIn\Test\ExampleCrmIntegration;

use Slim\App;
use SocialSignIn\ExampleCrmIntegration\Controller\IFrameController;
use SocialSignIn\ExampleCrmIntegration\Controller\SearchController;
use SocialSignIn\ExampleCrmIntegration\Person\RepositoryInterface;

class ContainerSetupTest extends \PHPUnit_Framework_TestCase
{

    public function testContainer()
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
