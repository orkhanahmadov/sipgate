<?php

namespace Orkhanahmadov\Sipgate;

use GuzzleHttp\Client;
use Orkhanahmadov\Sipgate\Resources\Device;
use Orkhanahmadov\Sipgate\Resources\History;
use Orkhanahmadov\Sipgate\Resources\User;

class Sipgate implements Telephony
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
     * Sipgate constructor.
     *
     * @param string|null $username
     * @param string|null $password
     */
    public function __construct(?string $username = null, ?string $password = null)
    {
        $this->username = $username;
        $this->password = $password;

        $this->client = new Client(['base_uri' => 'https://api.sipgate.com/v2/']);
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return Sipgate
     */
    public function setBasicAuthCredentials(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;

        return $this;
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return array|null
     */
    public function account(): ?array
    {
        return $this->sendRequest('account');
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return array
     */
    public function users(): array
    {
        $response = $this->sendRequest('users');

        $users = [];
        foreach ($response['items'] as $user) {
            array_push($users, new User($user));
        }

        return $users;
    }

    /**
     * @param User|string $user
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return array
     */
    public function devices($user): array
    {
        $userId = $user instanceof User ? $user->id : $user;

        $response = $this->sendRequest($userId.'/devices');

        $devices = [];
        foreach ($response['items'] as $device) {
            array_push($devices, new Device($user, $device));
        }

        return $devices;
    }

    /**
     * @param Device|string $device
     * @param string|int    $callee
     * @param array         $options
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return string
     */
    public function initiateCall($device, $callee, array $options = []): string
    {
        $response = $this->sendRequest('sessions/calls', 'POST', [
            'json' => [
                'caller'   => $device instanceof Device ? $device->id : $device,
                'callee'   => $callee,
                'callerId' => isset($options['callerId']) ? $options['callerId'] : null,
            ],
        ]);

        if (isset($options['recording'], $options['recording']['value'], $options['recording']['announcement'])) {
            $this->recordCall(
                $response['sessionId'],
                $options['recording']['value'],
                $options['recording']['announcement']
            );
        }

        return $response['sessionId'];
    }

    /**
     * @param string $callId
     * @param bool   $value
     * @param bool   $announcement
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return bool
     */
    public function recordCall(string $callId, bool $value, bool $announcement): bool
    {
        $this->sendRequest('calls/'.$callId.'/recording', 'PUT', [
            'json' => [
                'value'        => $value,
                'announcement' => $announcement,
            ],
        ]);

        return $value;
    }

    /**
     * @param array $options
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return array
     */
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
     * @param Client $client
     */
    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }
}
