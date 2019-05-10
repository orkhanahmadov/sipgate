<?php

namespace Orkhanahmadov\Sipgate;

use GuzzleHttp\Client;
use Orkhanahmadov\Sipgate\Resources\Device;
use Orkhanahmadov\Sipgate\Resources\History;
use Orkhanahmadov\Sipgate\Resources\User;

class Sipgate implements SipgateInterface
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

    public function account(): array
    {
        return $this->sendRequest('account');
    }

    public function users(): array
    {
        $response = $this->sendRequest('users');

        $users = [];
        foreach ($response['items'] as $user) {
            array_push($users, new User($user));
        }

        return $users;
    }

    public function devices(User $user): array
    {
        $response = $this->sendRequest($user->id.'/devices');

        $devices = [];
        foreach ($response['items'] as $device) {
            array_push($devices, new Device($user, $device));
        }

        return $devices;
    }

    public function initiateCall(Device $device, $callee, $callerId = null)
    {
        $response = $this->sendRequest('sessions/calls', 'POST', [
            'json' => [
                'deviceId' => $device->id,
                'caller'   => $device->user->id,
                'callerId' => $callerId,
                'callee'   => $callee,
            ],
        ]);

        return $response;
    }

    public function history(array $options = []): array
    {
        $response = $this->sendRequest('history', 'GET', ['query' => $options]);

        $history = [];
        foreach ($response['items'] as $item) {
            array_push($history, new History($item));
        }

        return $history;
    }

    /**
     * @param string $url
     * @param string $method
     * @param array  $options
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return array
     */
    private function sendRequest(string $url, string $method = 'GET', array $options = []): array
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
     * @param Client $client
     */
    public function setClient(Client $client): void
    {
        $this->client = $client;
    }
}
