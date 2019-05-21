<?php

namespace Orkhanahmadov\Sipgate\Tests\Unit;

use BlastCloud\Guzzler\UsesGuzzler;
use GuzzleHttp\Psr7\Response;
use Orkhanahmadov\Sipgate\Resources\Call;
use Orkhanahmadov\Sipgate\Resources\Device;
use Orkhanahmadov\Sipgate\Resources\History;
use Orkhanahmadov\Sipgate\Resources\User;
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

        $this->sipgate = new Sipgate(
            getenv('SIPGATE_USERNAME'),
            getenv('SIPGATE_PASSWORD')
        );
        $this->sipgate->setClient($this->guzzler->getClient(['base_uri' => $this->sipgateBaseUri]));
    }

    public function test_setBasicAuthCredentials()
    {
        $sipgate = new Sipgate();
        $sipgate->setBasicAuthCredentials('new user', 'new password');

        $this->assertEquals('new user', $sipgate->getUsername());
        $this->assertEquals('new password', $sipgate->getPassword());
    }

    public function test_account()
    {
        $this->guzzler
            ->expects($this->once())
            ->get('https://api.sipgate.com/v2/account')
            ->willRespond(new Response(200, [], '{"company":"some company","verified":true}'));

        $account = $this->sipgate->account();
        $this->assertEquals('some company', $account['company']);
        $this->assertTrue($account['verified']);
    }

    public function test_users()
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

    public function test_devices()
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

    public function test_calls()
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

    public function test_initiateCall()
    {
        $this->guzzler
            ->expects($this->once())
            ->post('https://api.sipgate.com/v2/sessions/calls')
            ->withBody('{"caller":"deviceId","callee":"123","callerId":"456"}')
            ->willRespond(new Response(200, [], '{"sessionId": "ABC1234"}'));

        $device = new Device(new User(), ['id' => 'deviceId']);
        $call = $this->sipgate->initiateCall($device, '123', ['callerId' => '456']);
        $this->assertEquals('ABC1234', $call);
    }

    public function test_initiateCall_with_recording()
    {
        $this->guzzler
            ->expects($this->once())
            ->post('https://api.sipgate.com/v2/sessions/calls')
            ->withBody('{"caller":"deviceId","callee":"123","callerId":null}')
            ->willRespond(new Response(200, [], '{"sessionId": "ABC1234"}'));

        $this->guzzler
            ->expects($this->once())
            ->put('https://api.sipgate.com/v2/calls/ABC1234/recording')
            ->withBody('{"value":true,"announcement":true}')
            ->willRespond(new Response(200, []));

        $call = $this->sipgate->initiateCall(
            new Device(new User(), ['id' => 'deviceId']),
            '123',
            ['recording' => ['value' => true, 'announcement' => true]]
        );
        $this->assertEquals('ABC1234', $call);
    }

    public function test_recordCall()
    {
        $this->guzzler
            ->expects($this->once())
            ->put('https://api.sipgate.com/v2/calls/ABC123/recording')
            ->withBody('{"value":true,"announcement":true}')
            ->willRespond(new Response(204, []));

        $this->assertTrue($this->sipgate->recordCall('ABC123', true, true));
    }

    public function test_history()
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
}
