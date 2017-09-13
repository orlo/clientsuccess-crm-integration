<?php

namespace SocialSignIn\ClientSuccessIntegration\Person;

use Assert\Assertion;
use GuzzleHttp\Client;

final class UserRepository implements RepositoryInterface
{
    private $token;

    private $api = "https://api.clientsuccess.com/v1/";

    private $username = "";

    private $password = "";

    private $employees = [];

    private $httpClient;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;

        $this->httpClient = new Client([
            'base_uri' => $this->api
        ]);
    }

    private function clientRequest($method, $uri, $options = []) {
        if (!empty($this->token)) {
            $options['headers'] = [
                'Authorization' => $this->token
            ];
        }
        $response = $this->httpClient->request($method, $uri, $options)->getBody()->getContents();
        Assertion::isJsonString($response, "Request to client success failed (non-json returned; can't get token)");
        return json_decode($response);
    }

    protected function login()
    {
        if (!empty($this->token)) {
            return;
        }

        $data = $this->clientRequest("POST", "auth", [
            "json" => [
                "username" => $this->username,
                "password" => $this->password
            ]
        ]);

        $this->token = $data->access_token;
    }

    private function getCurrentEmployee($email)
    {
        $this->login();

        if (empty($this->employees)) {
            $this->employees = $this->clientRequest("GET", "employees");
        }

        foreach ($this->employees as $employee) {
            if (strtolower($employee->email) == strtolower($email)) {
                return $employee;
            }
        }

        return $this->employees[0];
    }

    /**
     * @param string $query
     *
     * @return Entity[]
     */
    public function search($term)
    {

        $this->login();

        $clients = $this->clientRequest('GET', 'contracts/search', [
            'query' => [
                'term' => $term
            ]
        ]);

        $persons = [];
        foreach ($clients as $contact) {
            $persons[] = new Entity($contact->clientId . ":" . $contact->id,
                $contact->firstName . " " . $contact->lastName);
        }

        return $persons;
    }

    public function addToDo($id, $name)
    {

        $this->login();

        $clientId = explode(":", $id)[0];

        $response = $this->clientRequest('POST', "clients/$clientId/to-dos", [
            'json' => [
                'name' => $name
            ]
        ]);

        var_dump($response);
        exit; // @todo
    }


    public function addNote($clientId, $contactID, $subject, $note)
    {

        $this->login();

        $this->clientRequest('POST', "clients/$clientId/interactions", [
            'json' => [
                'createdByEmployeeId' => $this->getCurrentEmployee("")->id,
                'interactionTypeId' => 1,
                'subject' => $subject,
                'clientId' => $clientId,
                'contactID' => $contactID,
                'note' => $note
            ]
        ]);

        return json_encode(['success' => true]);

    }

    /**
     * @param string $id
     *
     * @return Entity|null
     */
    public function get($id)
    {

        $this->login();
        
        $clientId = explode(":", $id)[0];
        $contactId = explode(":", $id)[1];

        $contacts = $this->clientRequest('GET', "clients/" . $clientId . "/contacts/" . $contactId);

        $clients = $this->clientRequest('GET', "clients/" . $clientId);

        $interactions = $this->clientRequest('GET', "clients/" . $clientId . "/interactions");

        $todos = $this->clientRequest('GET', "clients/" . $clientId . "/to-dos");

        $subscriptions = $this->clientRequest('GET', "subscriptions", [
            'query' => [
                'clientId' => $clientId
            ]
        ]);

        $data = $contacts;
        $data->client = $clients;
        $data->interactions = $interactions;
        $data->subscriptions = $subscriptions;
        $data->todos = $todos;

        return $data;
    }
}
