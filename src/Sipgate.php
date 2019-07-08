<?php

namespace Orkhanahmadov\Sipgate;

use GuzzleHttp\Client;

class Sipgate implements Telephony
{
    use Traits\SendsRequest;

    /**
     * @var string|null
     */
    private $username = null;
    /**
     * @var string|null
     */
    private $password = null;
    /**
     * @var \GuzzleHttp\Client
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
     * Sets basic auth credentials.
     *
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
     * Returns account details.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return array|null
     */
    public function account(): ?array
    {
        return $this->sendRequest('account');
    }

    /**
     * Returns all created users.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return array
     */
    public function users(): array
    {
        $response = $this->sendRequest('users');

        $users = [];
        foreach ($response['items'] as $user) {
            array_push($users, new Resources\User($user));
        }

        return $users;
    }

    /**
     * Returns user devices.
     *
     * @param Resources\User|string $user
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return array
     */
    public function devices($user): array
    {
        $userId = $user instanceof Resources\User ? $user->id : $user;

        $response = $this->sendRequest($userId.'/devices');

        $devices = [];
        foreach ($response['items'] as $device) {
            array_push($devices, new Resources\Device($user, $device));
        }

        return $devices;
    }

    /**
     * Returns currently established calls.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return array
     */
    public function calls(): array
    {
        $response = $this->sendRequest('calls');

        $calls = [];
        foreach ($response['data'] as $call) {
            array_push($calls, new Resources\Call($call));
        }

        return $calls;
    }

    /**
     * Initiates new call and returns session ID.
     *
     * @param Resources\Device|string $device
     * @param string|int              $callee
     * @param string|int|null         $callerId
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return string
     */
    public function initiateCall($device, $callee, $callerId = null): string
    {
        $response = $this->sendRequest('sessions/calls', 'POST', [
            'json' => [
                'caller'   => $device instanceof Resources\Device ? $device->id : $device,
                'callee'   => $callee,
                'callerId' => $callerId,
            ],
        ]);

        return $response['sessionId'];
    }

    /**
     * Hangs up active call.
     *
     * @param string $callId
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return bool
     */
    public function hangupCall(string $callId): bool
    {
        $this->sendRequest('calls/'.$callId, 'DELETE');

        return true;
    }

    /**
     * Starts or stops call recording.
     *
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
     * Returns call history.
     *
     * @param array $options
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return array
     */
    public function history(array $options = []): array
    {
        $response = $this->sendRequest('history?'.$this->historyQueryString($options), 'GET');

        $history = [];
        foreach ($response['items'] as $item) {
            array_push($history, new Resources\History($item));
        }

        return $history;
    }

    /**
     * Generates history query string per SIPGate requirements
     *
     * @param array $options
     *
     * @return string
     */
    private function historyQueryString(array $options)
    {
        $queryString = [];

        foreach ($options as $name => $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    array_push($queryString, $name.'='.$item);
                }
            } else {
                array_push($queryString, $name.'='.$value);
            }
        }

        return implode('&', $queryString);
    }

    /**
     * Sets base auth username.
     *
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * Sets base auth password.
     *
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }
}
