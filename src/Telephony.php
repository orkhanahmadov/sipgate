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
     * @return array
     */
    public function account(): array;

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
     * @param Device $device
     * @param $callerNumber
     * @param $callee
     *
     * @return mixed
     */
    public function initiateCall(Device $device, $callerNumber, $callee);

    /**
     * @param array $options
     *
     * @return array
     */
    public function history(array $options = []): array;
}
