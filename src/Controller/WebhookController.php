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
        
        $shared_secret = getenv('SHARED_SECRET');
        if (empty($shared_secret)) {
            throw new \InvalidArgumentException("SHARED_SECRET not defined in environment. Cannot continue.");
        }
        
        if ($notification->isValid($shared_secret)) { // is it really from SocialSignIn ?
          
            file_put_contents('/tmp/cs_log.txt', json_encode($notification->getPayload()).PHP_EOL , FILE_APPEND | LOCK_EX);
            
            return $response->withJson([
                'verification-hash' => $notification->generateVerificationHash($shared_secret),
            ], 200);
        }
        return $response->withJson(['error' => 'hash mismatch', $notification], 400); // bad request
    }
}
