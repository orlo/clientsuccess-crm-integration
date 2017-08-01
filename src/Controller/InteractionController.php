<?php

namespace SocialSignIn\ExampleCrmIntegration\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

final class InteractionController
{

    private $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(Request $request, Response $response)
    {
        $note = $request->getParam('note', null);

        if (empty($note)) {
            throw new \InvalidArgumentException("note empty or not specified");
        }
        
        return $response->withJson([
            'results' => $note
        ], 200);
    }
}
