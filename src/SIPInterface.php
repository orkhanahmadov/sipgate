<?php

namespace Orkhanahmadov\Sipgate;

use Orkhanahmadov\Sipgate\Resources\Device;

interface SIPInterface
{
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
     * @return array
     */
    public function devices($user): array;

    /**
     * @param Device $device
     * @param $callerNumber
     * @param $callee
     * @return mixed
     */
    public function initiateCall(Device $device, $callerNumber, $callee);

    /**
     * @param array $options
     * @return array
     */
    public function history(array $options = []): array;
}
