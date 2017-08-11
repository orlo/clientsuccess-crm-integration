<?php

namespace SocialSignIn\ExampleCrmIntegration\Controller;

use Slim\Http\Request;
use Slim\Http\Response;
use SocialSignIn\ExampleCrmIntegration\Person\RepositoryInterface;

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
        $clientId = $request->getParam('client_id', null);
        $personId = $request->getParam('person_id', null);
        $subject = $request->getParam('subject', null);
        

        if (empty($note)) {
            throw new \InvalidArgumentException("note empty or not specified");
        }
        if (empty($clientId)) {
            throw new \InvalidArgumentException("client_id empty or not specified");
        }
        if (empty($personId)) {
            throw new \InvalidArgumentException("person_id empty or not specified");
        }
        if (empty($subject)) {
            throw new \InvalidArgumentException("subject empty or not specified");
        }
        
        $result = $this->repository->addNote($clientId, $personId, $subject, $note);
        
        return $response->withJson($result, 200);
    }
}
