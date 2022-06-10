<?php

namespace SocialSignIn\Test\ClientSuccessIntegration\Authentication;

use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;
use SocialSignIn\ClientSuccessIntegration\Authentication\SignatureAuthentication;

/**
 * @covers \SocialSignIn\ClientSuccessIntegration\Authentication\SignatureAuthentication
 */
class SignatureAuthenticationTest extends TestCase
{

    private $sharedSecret;
    private $middleware;

    public function setUp(): void
    {
        $this->sharedSecret = md5(random_bytes(32));
        $this->middleware = new SignatureAuthentication($this->sharedSecret);
    }

    public function testInvalidSharedSecret(): void
    {
        $this->expectExceptionMessage('Expected $sharedSecret to be non-empty string.');
        $this->expectException(\Exception::class);
        new SignatureAuthentication(null);
    }

    public function testMissingQueryParameters(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'QUERY_STRING' => ''
            ])
        );

        /** @var Response $response */
        $response = call_user_func($this->middleware, $request, new Response(), function () {
        });

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testInvalidSignature(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'QUERY_STRING' => $this->sign(['message_sentiment' => '1234'], 'incorrect-secret')
            ])
        );

        /** @var Response $response */
        $response = call_user_func($this->middleware, $request, new Response(), function () {
        });

        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testExpired(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'QUERY_STRING' => $this->sign(['message_sentiment' => '1234'], $this->sharedSecret, -3600)
            ])
        );

        /** @var Response $response */
        $response = call_user_func($this->middleware, $request, new Response(), function () {
        });

        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testSuccess(): void
    {
        $request = Request::createFromEnvironment(
            Environment::mock([
                'QUERY_STRING' => $this->sign(['message_sentiment' => '1234'], $this->sharedSecret)
            ])
        );

        $cb = function ($request, Response $response) {
            return $response->withStatus(200);
        };

        /** @var Response $response */
        $response = call_user_func($this->middleware, $request, new Response(), $cb);

        $this->assertEquals(200, $response->getStatusCode());
    }


    private function sign(array $params, $secret, $ttl = 3600): string
    {
        $params['expires'] = time() + $ttl;
        $params['sig'] = hash_hmac('sha256', http_build_query($params), $secret);
        return http_build_query($params);
    }
}
