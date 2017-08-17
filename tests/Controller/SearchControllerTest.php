<?php

namespace SocialSignIn\Test\ClientSuccessIntegration\Controller;

use Mockery as m;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;
use SocialSignIn\ClientSuccessIntegration\Controller\SearchController;
use SocialSignIn\ClientSuccessIntegration\Person\Entity;
use SocialSignIn\ClientSuccessIntegration\Person\RepositoryInterface;

/**
 * @covers \SocialSignIn\ClientSuccessIntegration\Controller\SearchController
 */
class SearchControllerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var SearchController
     */
    private $controller;

    /**
     * @var RepositoryInterface|m\Mock
     */
    private $repository;

    public function setUp()
    {
        $this->repository = m::mock(RepositoryInterface::class);
        $this->controller = new SearchController($this->repository);
    }

    public function tearDown()
    {
        m::close();
    }

    public function testBasic()
    {
        $this->repository->shouldReceive('search')
            ->withArgs(['john'])
            ->once()
            ->andReturn($persons = [new Entity('1', 'John'), new Entity('2', 'Johnny')]);

        $request = Request::createFromEnvironment(
            Environment::mock([
                'QUERY_STRING' => 'q=john'
            ])
        );

        $response = new Response();

        /** @var Response $response */
        $response = call_user_func($this->controller, $request, $response);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            '{"results": [{"id":"1","name":"John"},{"id":"2","name":"Johnny"}]}',
            (string)$response->getBody()
        );
    }

    public function testMissingQueryIsError()
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'QUERY_STRING' => ''
            ])
        );

        $response = new Response();

        /** @var Response $response */

        // no ?q=xxx
        $this->setExpectedException(\InvalidArgumentException::class);
        $response = call_user_func($this->controller, $request, $response);
    }
}
