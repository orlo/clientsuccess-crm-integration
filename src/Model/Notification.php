<?php

namespace SocialSignIn\ClientSuccessIntegration\Model;

use Ramsey\Uuid\Uuid;

class Notification
{

    private $webhook_uuid = null;
    private $hash = null;
    private $payload = null;

    public static function createFromHttpRequest(\Psr\Http\Message\RequestInterface $request)
    {
        $uuid = null;
        $hash = null;
        $payload = null;

        if ($request->hasHeader('SocialSignIn-HookId')) {
            $uuid = Uuid::fromString($request->getHeader('SocialSignIn-HookId')[0]);
            $uuid = $uuid->toString();
        }

        if ($request->hasHeader('SocialSignIn-Hash')) {
            $hash = $request->getHeader('SocialSignIn-Hash')[0];
        }

        $payload = "" . $request->getBody();

        return new Notification($uuid, $payload, $hash);
    }


    /**
     * Notification constructor.
     * @param string $webhook_uuid
     * @param string $payload (json encoded data)
     * @param string $hash
     */
    public function __construct(
        $webhook_uuid,
        $payload,
        $hash
    ) {
        $this->hash = $hash; // SocialSignIn-Hash
        $this->webhook_uuid = $webhook_uuid; // SocialSignIn-HookId
        $this->payload = $payload; // body of the http request.
    }

    /**
     * @param string $shared_secret
     * @return boolean
     */
    public function isValid($shared_secret)
    {
        // the hash in the http header should be a sha256 hash of the body (payload) and the shared secret.
        return hash_hmac('sha256', $this->payload, $shared_secret) === $this->hash;
    }

    /**
     * @param string $shared_secret
     * @return string
     */
    public function generateVerificationHash($shared_secret)
    {
        // Verification hash is a sha25 hash of the http header hash and our shared secret.
        return hash_hmac('sha256', $this->hash, $shared_secret);
    }


    /**
     * @return string
     */
    public function getPayload()
    {
        return $this->payload;
    }

    public function getWebHookUUID()
    {
        return $this->webhook_uuid;
    }
}
