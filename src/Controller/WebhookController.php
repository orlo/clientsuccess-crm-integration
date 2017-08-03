<?php

namespace SocialSignIn\ExampleCrmIntegration\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

final class WebhookController
{

    public function __construct()
    {
    }

    public function __invoke(Request $request, Response $response)
    {
      
        if (strtolower($request->getMethod()) !== 'post') {
            throw new \InvalidArgumentException("Post required.");
        }
        $notification = \SocialSignIn\ExampleCrmIntegration\Model\Notification::createFromHttpRequest($request);
        $body = "" . $request->getBody();
        
        $shared_secret = getenv('SHARED_SECRET');
        if (empty($secret)) {
            throw new \InvalidArgumentException("SHARED_SECRET not defined in environment. Cannot continue.");
        }
        
        if ($notification->isValid($shared_secret)) { // is it really from SocialSignIn ?
          
            $notification->getPayload();
            
            return $response->withJson([
                'verification-hash' => $notification->generateVerificationHash($shared_secret),
            ], 200);
        }
        return $response->withJson(['error' => 'hash mismatch', $notification], 400); // bad request
    }
}
