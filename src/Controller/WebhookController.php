<?php

namespace SocialSignIn\ClientSuccessIntegration\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

final class WebhookController
{

    private $shared_secret;

    public function __construct($shared_secret)
    {
        $this->shared_secret = $shared_secret;
    }

    public function __invoke(Request $request, Response $response)
    {

        if (strtolower($request->getMethod()) !== 'post') {
            throw new \InvalidArgumentException("Post required.");
        }
        $notification = \SocialSignIn\ClientSuccessIntegration\Model\Notification::createFromHttpRequest($request);

        $shared_secret = $this->shared_secret;

        if (empty($shared_secret)) {
            throw new \InvalidArgumentException("SHARED_SECRET not defined in environment. Cannot continue.");
        }

        if ($notification->isValid($shared_secret)) { // is it really from SocialSignIn ?

            file_put_contents(
                '/tmp/cs_log.txt',
                json_encode($notification->getPayload()) . PHP_EOL,
                FILE_APPEND | LOCK_EX
            );

            return $response->withJson([
                'verification-hash' => $notification->generateVerificationHash($shared_secret),
            ], 200);
        }
        return $response->withJson(['error' => 'hash mismatch', $notification], 400); // bad request
    }
}
