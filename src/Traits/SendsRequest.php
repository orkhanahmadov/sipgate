<?php

namespace Orkhanahmadov\Sipgate\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

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
     * Sets Guzzle client.
     *
     * @param Client $client
     */
    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    /**
     * Sends API requests to sipgate endpoint.
     *
     * @param string $url
     * @param string $method
     * @param array $options
     *
     * @return array|null
     * @throws GuzzleException
     *
     */
    private function sendRequest(string $url, string $method = 'GET', array $options = []): ?array
    {
        $response = $this->client->request($method, $url, array_merge(
            [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'auth' => [$this->username, $this->password],
            ],
            $options
        ));

        return json_decode($response->getBody()->getContents(), true);
    }
}
