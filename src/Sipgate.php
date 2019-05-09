<?php

namespace Orkhanahmadov\Sipgate;

use GuzzleHttp\Client;

class Sipgate
{
    /**
     * @var string
     */
    private $username;
    /**
     * @var string
     */
    private $password;
    /**
     * @var Client
     */
    private $client;

    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;

        $this->client = new Client(['base_uri' => 'https://api.sipgate.com/v2/']);
    }

    public function account()
    {
        return $this->sendRequest('account', 'GET');
    }

    public function users()
    {
        $result = $this->sendRequest('users', 'GET');

        $users = [];
        foreach ($result['items'] as $user) {
            array_push($users, new User($user));
        }

        return $users;
    }

    /**
     * @param string $url
     * @param string $method
     * @param array $options
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function sendRequest(string $url, string $method = 'POST', array $options = []): array
    {
        $response = $this->client->request($method, $url, array_merge(
            [
                'headers' => ['Accept' => 'application/json'],
                'auth' => [$this->username, $this->password]
            ],
            $options
        ));

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param Client $client
     */
    public function setClient(Client $client): void
    {
        $this->client = $client;
    }
}
