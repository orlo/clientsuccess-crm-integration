<?php

namespace SocialSignIn\Test\ExampleCrmIntegration\Controller;

use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;
use SocialSignIn\ExampleCrmIntegration\Controller\IFrameController;
use Mockery as m;
use SocialSignIn\ExampleCrmIntegration\Person\Entity;
use SocialSignIn\ExampleCrmIntegration\Person\RepositoryInterface;

/**
 * @covers \SocialSignIn\ExampleCrmIntegration\Controller\IFrameController
 */
class IFrameControllerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var IFrameController
     */
    private $controller;

    /**
     * @var \Twig_Environment|m\Mock
     */
    private $twig;

    /**
     * @var RepositoryInterface|m\Mock
     */
    private $repository;

    public function setUp()
    {
        $this->twig = m::mock(\Twig_Environment::class);
        $this->repository = m::mock(RepositoryInterface::class);
        $this->controller = new IFrameController($this->twig, $this->repository);
    }

    public function tearDown()
    {
        m::close();
    }

    public function testItCanReturnHtml()
    {
        $this->repository->shouldReceive('get')
            ->withArgs(['1'])
            ->once()
            ->andReturn($person = new Entity('1', 'John'));

        $this->twig->shouldReceive('render')
            ->once()
            ->withArgs(['i-frame.twig', ['person' => $person]])
            ->andReturn('<html></html>');

        $request = Request::createFromEnvironment(
            Environment::mock([
                'QUERY_STRING' => 'id=1'
            ])
        );

        $response = new Response();

        /** @var Response $response */
        $response = call_user_func($this->controller, $request, $response);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('<html></html>', (string)$response->getBody());
    }

    public function testMissingIdIsError()
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'QUERY_STRING' => ''
            ])
        );

        $response = new Response();

        /** @var Response $response */
        $response = call_user_func($this->controller, $request, $response);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testPersonNotFound()
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'QUERY_STRING' => 'id=1'
            ])
        );

        $this->repository->shouldReceive('get')
            ->withArgs(['1'])
            ->once()
            ->andReturn(null);

        $response = new Response();

        /** @var Response $response */
        $response = call_user_func($this->controller, $request, $response);

        $this->assertEquals(404, $response->getStatusCode());
    }
}
