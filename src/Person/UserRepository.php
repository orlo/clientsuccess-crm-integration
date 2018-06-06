<?php

namespace SocialSignIn\ClientSuccessIntegration\Person;

use Assert\Assertion;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;

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

    private function addHeaders($options)
    {
        if (!empty($this->token)) {
            $options['headers'] = [
                'Authorization' => $this->token
            ];
        }
        return $options;
    }

    private function clientRequest($method, $uri, $options = [])
    {
        $options = $this->addHeaders($options);
        $response = $this->httpClient->request($method, $uri, $options)->getBody()->getContents();
        Assertion::isJsonString($response, "Request to client success failed (non-json returned; can't get token)");
        return json_decode($response);
    }

    private function clientRequestAsync($method, $uri, $options = [])
    {
        $options = $this->addHeaders($options);
        return $this->httpClient->requestAsync($method, $uri, $options);
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

        $clients = $this->clientRequest('GET', 'contacts/search', [
            'query' => [
                'term' => $term
            ]
        ]);

        $persons = [];
        foreach ($clients as $contact) {
            $persons[] = new Entity(
                $contact->clientId . ":" . $contact->id,
                $contact->firstName . " " . $contact->lastName
            );
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

        $promises = [
            'contact'       => $this->clientRequestAsync('GET', "clients/" . $clientId . "/contacts/" . $contactId),
            'client'        => $this->clientRequestAsync('GET', "clients/" . $clientId),
            'interactions'  => $this->clientRequestAsync('GET', "clients/" . $clientId . "/interactions"),
            'todos'         => $this->clientRequestAsync('GET', "clients/" . $clientId . "/to-dos"),
            'subscriptions' => $this->clientRequestAsync('GET', "subscriptions", [
                'query' => [
                    'clientId' => $clientId
                ]
            ])
        ];

        $results = Promise\unwrap($promises);

        $data = json_decode($results['contact']->getBody()->getContents());
        $data->client = json_decode($results['client']->getBody()->getContents());
        $data->interactions = json_decode($results['interactions']->getBody()->getContents());
        $data->subscriptions = json_decode($results['subscriptions']->getBody()->getContents());
        $data->todos = json_decode($results['todos']->getBody()->getContents());

        return $data;
    }
}
