<?php

namespace Orkhanahmadov\Sipgate\Tests\Unit;

use BlastCloud\Guzzler\UsesGuzzler;
use GuzzleHttp\Psr7\Response;
use Orkhanahmadov\Sipgate\Exceptions\PropertyNotFoundException;
use Orkhanahmadov\Sipgate\Sipgate;
use Orkhanahmadov\Sipgate\Tests\TestCase;
use Orkhanahmadov\Sipgate\User;

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

        $this->expectException(PropertyNotFoundException::class);
        $this->expectExceptionMessage('fakeProperty property not found.');
        $users[0]->fakeProperty;
    }
}
