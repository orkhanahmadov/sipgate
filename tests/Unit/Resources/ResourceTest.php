<?php

namespace Orkhanahmadov\Sipgate\Tests\Unit;

use Orkhanahmadov\Sipgate\Exceptions\ResourcePropertyNotFoundException;
use Orkhanahmadov\Sipgate\Resources\Resource;
use Orkhanahmadov\Sipgate\Tests\TestCase;

class ResourceTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $stub;

    protected function setUp()
    {
        parent::setUp();

        $this->stub = $this->getMockBuilder(Resource::class)
            ->setMethods(['__construct'])
            ->setConstructorArgs([[
                'prop1' => 'val1',
                'prop2' => 'val2',
            ]])
            ->getMock();
    }

    public function test_throws_exception_if_property_is_not_set()
    {
        $this->expectException(ResourcePropertyNotFoundException::class);
        $this->expectExceptionMessage('unsetProp property not found.');

        $this->assertEquals('val1', $this->stub->prop1);
        $this->assertEquals('val2', $this->stub->prop2);
        $this->stub->unsetProp;
    }

    public function test_json_serializable()
    {
        $this->assertEquals([
            'prop1' => 'val1',
            'prop2' => 'val2',
        ], $this->stub->jsonSerialize());
    }
}
