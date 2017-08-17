<?php

namespace SocialSignIn\ClientSuccessIntegration\Controller;

use Assert\Assertion;
use Slim\Http\Request;
use Slim\Http\Response;
use SocialSignIn\ClientSuccessIntegration\Person\UserRepository;

final class InteractionController
{

    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(Request $request, Response $response)
    {
        $note = $request->getParam('note', null);
        $clientId = $request->getParam('client_id', null);
        $personId = $request->getParam('person_id', null);
        $subject = $request->getParam('subject', null);


        Assertion::notEmpty($note, "note empty or not specified");
        Assertion::notEmpty($clientId, "client_id empty or not specified");
        Assertion::notEmpty($personId, "person_id empty or not specified");
        Assertion::notEmpty($subject, "subject empty or not specified");

        $result = $this->repository->addNote($clientId, $personId, $subject, $note);
        
        return $response->withJson($result, 200);
    }
}
