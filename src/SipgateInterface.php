<?php

namespace Orkhanahmadov\Sipgate;

use Orkhanahmadov\Sipgate\Resources\Device;
use Orkhanahmadov\Sipgate\Resources\User;

interface SipgateInterface
{
    public function account(): array;

    public function users(): array;

    public function devices(User $user): array;

    public function initiateCall(Device $device, $callerNumber, $callee);

    public function history(array $options = []): array;
}
