<?php

namespace Orkhanahmadov\Sipgate\Tests\Unit;

use BlastCloud\Guzzler\UsesGuzzler;
use Orkhanahmadov\Sipgate\Sipgate;
use Orkhanahmadov\Sipgate\Tests\TestCase;

class SipgateTest extends TestCase
{
    use UsesGuzzler;

    public function test_experiment()
    {
        $sipgate = new Sipgate();
        $sipgate->setClient($this->guzzler->getClient());
    }
}
