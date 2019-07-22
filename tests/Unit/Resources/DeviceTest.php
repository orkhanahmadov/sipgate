<?php

namespace Innoscripta\Sipgate\Tests\Unit;

use Orkhanahmadov\Sipgate\Resources\User;
use Orkhanahmadov\Sipgate\Tests\TestCase;
use Orkhanahmadov\Sipgate\Resources\Device;

class DeviceTest extends TestCase
{
    public function test_userId()
    {
        $device = new Device(new User(['id' => '123']));
        $this->assertEquals('123', $device->userId());

        $device = new Device('456');
        $this->assertEquals('456', $device->userId());
    }
}
