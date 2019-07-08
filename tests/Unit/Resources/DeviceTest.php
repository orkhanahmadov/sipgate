<?php

namespace Innoscripta\Sipgate\Tests\Unit;

use Innoscripta\Sipgate\Resources\Device;
use Innoscripta\Sipgate\Resources\User;
use Innoscripta\Sipgate\Tests\TestCase;

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
