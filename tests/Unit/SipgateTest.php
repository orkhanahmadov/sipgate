<?php

namespace Orkhanahmadov\Sipgate\Tests\Unit;

use BlastCloud\Guzzler\UsesGuzzler;
use GuzzleHttp\Psr7\Response;
use Orkhanahmadov\Sipgate\Sipgate;
use Orkhanahmadov\Sipgate\Tests\TestCase;

class SipgateTest extends TestCase
{
    use UsesGuzzler;

    /**
     * @var Sipgate
     */
    private $sipgate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sipgate = new Sipgate(getenv('SIPGATE_USERNAME'), getenv('SIPGATE_PASSWORD'));
        $this->sipgate->setClient($this->guzzler->getClient(['base_uri' => $this->sipgateBaseUri]));
    }

    public function test_account()
    {
        $this->guzzler
            ->expects($this->once())
            ->get('https://api.sipgate.com/v2/account')
            ->willRespond(new Response(200, [], file_get_contents(__DIR__.'/../__fixtures__/account.json')));

        $response = $this->sipgate->account();
        $this->assertEquals('innoscripta GmbH', $response['company']);
        $this->assertEquals('TEAM', $response['mainProductType']);
        $this->assertEquals('', $response['logoUrl']);
        $this->assertTrue($response['verified']);
    }
}
