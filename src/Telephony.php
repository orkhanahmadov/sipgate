<?php

namespace Orkhanahmadov\Sipgate;

use Orkhanahmadov\Sipgate\Resources\Device;

interface Telephony
{
    /**
     * Sets basic auth credentials.
     *
     * @param string $username
     * @param string $password
     *
     * @return mixed
     */
    public function setBasicAuthCredentials(string $username, string $password);

    /**
     * Returns account details.
     *
     * @return array|null
     */
    public function account(): ?array;

    /**
     * Returns all created users.
     *
     * @return array
     */
    public function users(): array;

    /**
     * Returns user devices.
     *
     * @param \Innoscripta\Sipgate\Resources\User|string $user
     *
     * @return array
     */
    public function devices($user): array;

    /**
     * Returns currently established calls.
     *
     * @return array
     */
    public function calls(): array;

    /**
     * Initiates new call and returns session ID.
     *
     * @param Device|string   $device
     * @param string|int      $callee
     * @param string|int|null $callerId
     *
     * @return string
     */
    public function initiateCall($device, $callee, $callerId = null): string;

    /**
     * Hangs up active call.
     *
     * @param string $callId
     *
     * @return bool
     */
    public function hangupCall(string $callId): bool;

    /**
     * Starts or stops call recording.
     *
     * @param string $callId
     * @param bool   $value
     * @param bool   $announcement
     *
     * @return bool
     */
    public function recordCall(string $callId, bool $value, bool $announcement): bool;

    /**
     * Returns call history.
     *
     * @param array $options
     *
     * @return array
     */
    public function history(array $options = []): array;
}
