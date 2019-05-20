<?php

namespace Orkhanahmadov\Sipgate\Tests\Unit;

use BlastCloud\Guzzler\UsesGuzzler;
use GuzzleHttp\Psr7\Response;
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

        $user = new User(['id' => 'fakeidhere']);
        $devices = $this->sipgate->devices($user);
        $this->assertIsArray($devices);
        $this->assertInstanceOf(Device::class, $devices[0]);
        $this->assertEquals('e1', $devices[0]->id);
    }

    public function test_initiateCall()
    {
        $this->guzzler
            ->expects($this->once())
            ->post('https://api.sipgate.com/v2/sessions/calls')
            ->willRespond(new Response(200, [], '{"sessionId": "ABC1234"}'));

        $user = new User(['id' => 'userId']);
        $device = new Device($user, ['id' => 'deviceId']);
        $call = $this->sipgate->initiateCall($device, '123', '456');
        $this->assertEquals('ABC1234', $call);
    }

    public function test_recordCall()
    {
        $this->guzzler
            ->expects($this->once())
            ->put('https://api.sipgate.com/v2/calls/ABC123/recording')
            ->withBody('{"announcement":true,"value":true}')
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
