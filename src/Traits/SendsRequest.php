<?php

namespace Orkhanahmadov\Sipgate\Traits;

use GuzzleHttp\Client;

trait SendsRequest
{
    /**
     * @var string|null
     */
    private $username = null;
    /**
     * @var string|null
     */
    private $password = null;
    /**
     * @var Client
     */
    private $client;

    /**
     * Sends API requests to sipgate endpoint.
     *
     * @param string $url
     * @param string $method
     * @param array  $options
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return array|null
     */
    private function sendRequest(string $url, string $method = 'GET', array $options = []): ?array
    {
        $response = $this->client->request($method, $url, array_merge(
            [
                'headers' => ['Accept' => 'application/json'],
                'auth'    => [$this->username, $this->password],
            ],
            $options
        ));

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Sets Guzzle client.
     *
     * @param Client $client
     */
    public function setClient(Client $client): void
    {
        $this->client = $client;
    }
}
