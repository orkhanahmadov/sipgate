<?php

namespace Orkhanahmadov\Sipgate\Tests\Unit;

use Orkhanahmadov\Sipgate\Exceptions\ResourcePropertyNotFoundException;
use Orkhanahmadov\Sipgate\Resources\Resource;
use Orkhanahmadov\Sipgate\Tests\TestCase;
use Orkhanahmadov\Sipgate\Resources\User;

class UserTest extends TestCase
{
    public function test_throws_exception_if_property_is_not_set()
    {
        $this->expectException(ResourcePropertyNotFoundException::class);
        $this->expectExceptionMessage('unsetProperty property not found.');

        $stub = $this->getMockForAbstractClass(Resource::class);

        $stub->unsetProperty;
    }
}
