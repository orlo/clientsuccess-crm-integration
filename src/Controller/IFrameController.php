<?php


namespace SocialSignIn\ClientSuccessIntegration\Controller;

use Slim\Http\Request;
use Slim\Http\Response;
use SocialSignIn\ClientSuccessIntegration\Person\RepositoryInterface;
use Twig\Environment;

final class IFrameController
{

    private $twig;
    private $repository;
    private $sharedSecret;

    public function __construct(Environment $twig, RepositoryInterface $repository, $sharedSecret)
    {
        $this->twig = $twig;
        $this->repository = $repository;
        $this->sharedSecret = $sharedSecret;
    }

    public function __invoke(Request $request, Response $response)
    {
        $requestParams = $request->getParams();

        if (empty($requestParams['id'])) {
            return $response->withStatus(400);
        }

        $person = $this->repository->get($requestParams['id']);
        if ($person === null) {
            return $response->withStatus(404);
        }

        $query = ['expires' => time() + 600];
        $query['sig'] = hash_hmac('sha256', http_build_query($query), $this->sharedSecret);

        $response->write($this->twig->render('i-frame.twig', [
            'person' => $person,
            'request' => $requestParams,
            'query' => http_build_query($query)
        ]));

        return $response;
    }
}
