<?php

namespace Orkhanahmadov\Sipgate;

use GuzzleHttp\Client;

class Sipgate
{
    /**
     * @var Client
     */
    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @param Client $client
     */
    public function setClient(Client $client): void
    {
        $this->client = $client;
    }
}
