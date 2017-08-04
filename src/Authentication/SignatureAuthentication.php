<?php

namespace SocialSignIn\ExampleCrmIntegration\Authentication;

use Slim\Http\Request;
use Slim\Http\Response;

final class SignatureAuthentication
{

    /**
     * @var array
     */
    private $passthrough;

    /**
     * @var string
     */
    private $sharedSecret;

    /**
     * @param string $sharedSecret
     *
     * @throws \Exception
     */
    public function __construct($sharedSecret, $passthrough = [])
    {
        if (!is_string($sharedSecret) || empty($sharedSecret)) {
            throw new \Exception('Expected $sharedSecret to be non-empty string.');
        }

        $this->sharedSecret = $sharedSecret;
        $this->passthrough = $passthrough;
  }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        foreach ($this->passthrough as $method => $target) {
          if ($method == $request->getMethod() && $target == $request->getRequestTarget()) {
            return $next($request, $response);
          }
        }
        
        $query = $request->getQueryParams();

        if (!isset($query['sig'])
            || !is_string($query['sig'])
            || !isset($query['expires'])
            || !is_string($query['expires'])
            || !ctype_digit($query['expires'])
        ) {
            return $response->withJson(['status' => 'error', 'error' => 'missing or invalid sig or expires params'], 400);
        }

        $signature = $query['sig'];
        unset($query['sig']);
        $allowedParams = [
            'message_social_network_type',
            'message_social_network_id',
            'message_author_social_network_id',
            'message_sentiment',
            'message_language_code',
            'message_socialsignin_id',
            'message_socialsignin_url',
            'message_author_socialsignin_id',
            
        ];
        foreach($allowedParams as $param) {
            unset($query[$param]);
        }

        $expected = hash_hmac('sha256', http_build_query($query), $this->sharedSecret);

        if ($expected != $signature) {
            error_log("Signature mismatch: $expected vs $signature");
            return $response->withJson(['status' => 'error', 'error' => 'Signature mismatch'], 403);
        }

        $now = time();
        if ($now > $query['expires']) {
            error_log("Request has expired. Clock skew or attempt at replay attack. $now vs {$query['expires']}");
            return $response->withJson(['status' => 'error', 'error' => 'request expired'], 403);
        }

        return $next($request, $response);
    }
}
