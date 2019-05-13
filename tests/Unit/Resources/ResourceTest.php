<?php

namespace Orkhanahmadov\Sipgate\Tests\Unit;

use Orkhanahmadov\Sipgate\Exceptions\ResourcePropertyNotFoundException;
use Orkhanahmadov\Sipgate\Resources\Resource;
use Orkhanahmadov\Sipgate\Tests\TestCase;

class ResourceTest extends TestCase
{
    public function test_throws_exception_if_property_is_not_set()
    {
        $this->expectException(ResourcePropertyNotFoundException::class);
        $this->expectExceptionMessage('unsetProp property not found.');

        $stub = $this->getMockBuilder(Resource::class)
            ->setMethods(['__construct'])
            ->setConstructorArgs([['setProp' => 'setVal']])
            ->getMock();

        $this->assertEquals('setVal', $stub->setProp);
        $stub->unsetProp;
    }

    public function test_json_serializable()
    {
        $stub = $this->getMockBuilder(Resource::class)
            ->setMethods(['__construct'])
            ->setConstructorArgs([[
                'prop1' => 'val1',
                'prop2' => 'val2'
            ]])
            ->getMock();

        $this->assertEquals([
            'prop1' => 'val1',
            'prop2' => 'val2'
        ], $stub->jsonSerialize());
    }
}
