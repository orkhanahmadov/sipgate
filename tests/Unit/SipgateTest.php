<?php

namespace Orkhanahmadov\Sipgate\Tests\Unit;

use BlastCloud\Guzzler\UsesGuzzler;
use GuzzleHttp\Psr7\Response;
use Orkhanahmadov\Sipgate\Resources\Device;
use Orkhanahmadov\Sipgate\Resources\History;
use Orkhanahmadov\Sipgate\Sipgate;
use Orkhanahmadov\Sipgate\Tests\TestCase;
use Orkhanahmadov\Sipgate\Resources\User;

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

    public function test_account()
    {
        $this->guzzler
            ->expects($this->once())
            ->get('https://api.sipgate.com/v2/account')
            ->willRespond(new Response(200, [], file_get_contents(__DIR__.'/../__fixtures__/account.json')));

        $account = $this->sipgate->account();
        $this->assertEquals('some company', $account['company']);
        $this->assertEquals('TEAM', $account['mainProductType']);
        $this->assertEquals('', $account['logoUrl']);
        $this->assertTrue($account['verified']);
    }

    public function test_users()
    {
        $this->guzzler
            ->expects($this->once())
            ->get('https://api.sipgate.com/v2/users')
            ->willRespond(new Response(200, [], file_get_contents(__DIR__.'/../__fixtures__/users.json')));

        $users = $this->sipgate->users();
        $this->assertIsArray($users);
        $this->assertInstanceOf(User::class, $users[0]);
        $this->assertEquals('w0', $users[0]->id);
        $this->assertEquals('First Name', $users[0]->firstname);
        $this->assertEquals('Last Name', $users[0]->lastname);
        $this->assertEquals('someone@gmail.com', $users[0]->email);
        $this->assertEquals('e2', $users[0]->defaultDevice);
        $this->assertFalse($users[0]->busyOnBusy);
        $this->assertEquals('123456', $users[0]->addressId);
        $this->assertIsArray($users[0]->directDialIds);
        $this->assertEquals('567890', $users[0]->directDialIds[0]);
        $this->assertTrue($users[0]->admin);
    }

    public function test_devices()
    {
        $this->guzzler
            ->expects($this->once())
            ->get('https://api.sipgate.com/v2/fakeidhere/devices')
            ->willRespond(new Response(200, [], file_get_contents(__DIR__.'/../__fixtures__/devices.json')));

        $user = new User(['id' => 'fakeidhere']);
        $devices = $this->sipgate->devices($user);
        $this->assertIsArray($devices);
        $this->assertInstanceOf(Device::class, $devices[0]);
        $this->assertEquals('e2', $devices[0]->id);
        $this->assertEquals('VoIP-Telefon von Firstname Lastname', $devices[0]->alias);
        $this->assertEquals('REGISTER', $devices[0]->type);
        $this->assertFalse($devices[0]->online);
        $this->assertFalse($devices[0]->dnd);
        $this->assertIsArray($devices[0]->activePhonelines);
        $this->assertEquals('p0', $devices[0]->activePhonelines[0]['id']);
        $this->assertEquals('Firstname Lastname', $devices[0]->activePhonelines[0]['alias']);
        $this->assertIsArray($devices[0]->activeGroups);
        $this->assertIsArray($devices[0]->credentials);
        $this->assertEquals('123456', $devices[0]->credentials['username']);
        $this->assertEquals('secret', $devices[0]->credentials['password']);
        $this->assertEquals('sipgate.de', $devices[0]->credentials['sipServer']);
        $this->assertEquals('sipgate.de', $devices[0]->credentials['outboundProxy']);
        $this->assertEquals('wss://tls01.sipgate.de', $devices[0]->credentials['sipServerWebsocketUrl']);
        $this->assertIsArray($devices[0]->registered);
        $this->assertEquals('567890', $devices[0]->emergencyAddressId);
        $this->assertEquals('https://api.sipgate.com/v2/addresses/567890', $devices[0]->addressUrl);
    }

    public function test_initiateCall()
    {
        $this->guzzler
            ->expects($this->once())
            ->post('https://api.sipgate.com/v2/sessions/calls')
            ->willRespond(new Response(200, [], file_get_contents(__DIR__.'/../__fixtures__/sessions/new_call.json')));

        $user = new User(['id' => 'userId']);
        $device = new Device($user, ['id' => 'deviceId']);
        $call = $this->sipgate->initiateCall($device, '123', '456');
        // todo: test response
    }

    public function test_history()
    {
        $this->guzzler
            ->expects($this->once())
            ->get('https://api.sipgate.com/v2/history')
            ->willRespond(new Response(200, [], file_get_contents(__DIR__.'/../__fixtures__/history.json')));

        $history = $this->sipgate->history([]);

        $this->assertIsArray($history);
        $this->assertInstanceOf(History::class, $history[0]);
        $this->assertEquals('123456', $history[0]->id);
        $this->assertEquals('+4911111', $history[0]->source);
        $this->assertEquals('+4988888', $history[0]->target);
        $this->assertEquals('source alias', $history[0]->sourceAlias);
        $this->assertEquals('target alias', $history[0]->targetAlias);
        $this->assertEquals('CALL', $history[0]->type);
        $this->assertEquals('2019-04-30T10:03:03Z', $history[0]->created);
        $this->assertEquals('2019-04-30T10:03:39Z', $history[0]->lastModified);
        $this->assertEquals('MISSED_OUTGOING', $history[0]->direction);
        $this->assertFalse($history[0]->incoming);
        $this->assertEquals('NOPICKUP', $history[0]->status);
        $this->assertIsArray($history[0]->connectionIds);
        $this->assertTrue($history[0]->read);
        $this->assertFalse($history[0]->archived);
        $this->assertEquals('something', $history[0]->note);
        $this->assertIsArray($history[0]->endpoints);
        $this->assertFalse($history[0]->starred);
        $this->assertIsArray($history[0]->labels);
        $this->assertEquals('54566B150B0C0D3A5D564', $history[0]->callId);
        $this->assertEquals('', $history[0]->recordingUrl);
        $this->assertIsArray($history[0]->recordings);
        $this->assertEquals(18, $history[0]->duration);
        $this->assertEquals('', $history[0]->responder);
        $this->assertEquals('', $history[0]->responderAlias);
    }
}
