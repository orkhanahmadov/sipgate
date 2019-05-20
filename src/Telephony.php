<?php

namespace Orkhanahmadov\Sipgate;

use Orkhanahmadov\Sipgate\Resources\Device;

interface Telephony
{
    /**
     * @param string $username
     * @param string $password
     *
     * @return mixed
     */
    public function setBasicAuthCredentials(string $username, string $password);

    /**
     * @return array|null
     */
    public function account(): ?array;

    /**
     * @return array
     */
    public function users(): array;

    /**
     * @param \Orkhanahmadov\Sipgate\Resources\User|string $user
     *
     * @return array
     */
    public function devices($user): array;

    /**
     * @param Device|string $device
     * @param string|int    $callee
     * @param array         $options
     *
     * @return string
     */
    public function initiateCall($device, $callee, array $options = []): string;

    /**
     * @param string $callId
     * @param bool   $value
     * @param bool   $announcement
     *
     * @return bool
     */
    public function recordCall(string $callId, bool $value, bool $announcement): bool;

    /**
     * @param array $options
     *
     * @return array
     */
    public function history(array $options = []): array;
}
