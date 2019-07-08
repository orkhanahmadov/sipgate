<?php

namespace Innoscripta\Sipgate\Tests\Unit;

use BlastCloud\Guzzler\UsesGuzzler;
use GuzzleHttp\Psr7\Response;
use Innoscripta\Sipgate\Resources\Call;
use Innoscripta\Sipgate\Resources\Device;
use Innoscripta\Sipgate\Resources\History;
use Innoscripta\Sipgate\Resources\User;
use Innoscripta\Sipgate\Sipgate;
use Innoscripta\Sipgate\Tests\TestCase;
use ReflectionMethod;

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

        $this->sipgate = new Sipgate(
            getenv('SIPGATE_USERNAME'),
            getenv('SIPGATE_PASSWORD')
        );
        $this->sipgate->setClient($this->guzzler->getClient(['base_uri' => $this->sipgateBaseUri]));
    }

    public function testSetBasicAuthCredentials()
    {
        $sipgate = new Sipgate();
        $sipgate->setBasicAuthCredentials('new user', 'new password');

        $this->assertEquals('new user', $sipgate->getUsername());
        $this->assertEquals('new password', $sipgate->getPassword());
    }

    public function testAccount()
    {
        $this->guzzler
            ->expects($this->once())
            ->get('https://api.sipgate.com/v2/account')
            ->willRespond(new Response(200, [], '{"company":"some company","verified":true}'));

        $account = $this->sipgate->account();
        $this->assertEquals('some company', $account['company']);
        $this->assertTrue($account['verified']);
    }

    public function testUsers()
    {
        $this->guzzler
            ->expects($this->once())
            ->get('https://api.sipgate.com/v2/users')
            ->willRespond(new Response(200, [], '{"items":[{"id":"w0"}]}'));

        $users = $this->sipgate->users();
        $this->assertIsArray($users);
        $this->assertInstanceOf(User::class, $users[0]);
        $this->assertEquals('w0', $users[0]->id);
    }

    public function testDevices()
    {
        $this->guzzler
            ->expects($this->once())
            ->get('https://api.sipgate.com/v2/fakeidhere/devices')
            ->willRespond(new Response(200, [], '{"items":[{"id": "e1"}]}'));

        $devices = $this->sipgate->devices(new User(['id' => 'fakeidhere']));
        $this->assertIsArray($devices);
        $this->assertInstanceOf(Device::class, $devices[0]);
        $this->assertEquals('e1', $devices[0]->id);
    }

    public function testCalls()
    {
        $this->guzzler
            ->expects($this->once())
            ->get('https://api.sipgate.com/v2/calls')
            ->willRespond(new Response(200, [], '{"data":[{"callId": "123ZXC"}]}'));

        $calls = $this->sipgate->calls();
        $this->assertIsArray($calls);
        $this->assertInstanceOf(Call::class, $calls[0]);
        $this->assertEquals('123ZXC', $calls[0]->callId);
    }

    public function testInitiateCall()
    {
        $this->guzzler
            ->expects($this->once())
            ->post('https://api.sipgate.com/v2/sessions/calls')
            ->withBody('{"caller":"deviceId","callee":"123","callerId":"456"}')
            ->willRespond(new Response(200, [], '{"sessionId": "ABC1234"}'));

        $device = new Device(new User(), ['id' => 'deviceId']);
        $call = $this->sipgate->initiateCall($device, '123', '456');
        $this->assertEquals('ABC1234', $call);
    }

    public function testHangupCall()
    {
        $this->guzzler
            ->expects($this->once())
            ->delete('https://api.sipgate.com/v2/calls/ZXC123')
            ->willRespond(new Response(204, []));

        $hangup = $this->sipgate->hangupCall('ZXC123');
        $this->assertTrue($hangup);
    }

    public function testRecordCall()
    {
        $this->guzzler
            ->expects($this->once())
            ->put('https://api.sipgate.com/v2/calls/ABC123/recording')
            ->withBody('{"value":true,"announcement":true}')
            ->willRespond(new Response(204, []));

        $this->assertTrue($this->sipgate->recordCall('ABC123', true, true));
    }

    public function testHistory()
    {
        $this->guzzler
            ->expects($this->once())
            ->get('https://api.sipgate.com/v2/history')
            ->willRespond(new Response(200, [], '{"items":[{"id":"123456"}]}'));

        $history = $this->sipgate->history([]);

        $this->assertIsArray($history);
        $this->assertInstanceOf(History::class, $history[0]);
        $this->assertEquals('123456', $history[0]->id);
    }

    public function testHistoryQueryString()
    {
        $method = new ReflectionMethod(Sipgate::class, 'historyQueryString');
        $method->setAccessible(true);

        $result = $method->invoke(new Sipgate(), [
            'key1' => 'val1',
            'key2' => [
                'val2',
                'val3',
            ],
            'key3' => 'val4',
        ]);

        $this->assertEquals('key1=val1&key2=val2&key2=val3&key3=val4', $result);
    }
}
