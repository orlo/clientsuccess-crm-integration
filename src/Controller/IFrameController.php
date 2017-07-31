<?php

namespace SocialSignIn\ExampleCrmIntegration\Controller;

use Slim\Http\Request;
use Slim\Http\Response;
use SocialSignIn\ExampleCrmIntegration\Person\RepositoryInterface;
use Twig_Environment;

final class IFrameController
{

    private $twig;
    private $repository;

    public function __construct(Twig_Environment $twig, RepositoryInterface $repository)
    {
        $this->twig = $twig;
        $this->repository = $repository;
    }

    public function __invoke(Request $request, Response $response)
    {
        
        $id = $request->getQueryParam('id', null);

        if (empty($id)) {
            return $response->withStatus(400);
        }

        $person = $this->repository->get($id);
        if ($person === null) {
            return $response->withStatus(404);
        }
        
        $response->write($this->twig->render('i-frame.twig', ['person' => $person]));
        
        return $response;
    }
}
