<?php

namespace SocialSignIn\ExampleCrmIntegration\Controller;

use Slim\Http\Request;
use Slim\Http\Response;
use SocialSignIn\ExampleCrmIntegration\Person\RepositoryInterface;

final class SearchController
{

    private $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(Request $request, Response $response)
    {
        $query = $request->getQueryParam('q', null);

        if (empty($query)) {
            throw new \InvalidArgumentException("q empty or not specified");
        }

        return $response->withJson([
            'results' => $this->repository->search($query)
        ], 200);
    }
}
