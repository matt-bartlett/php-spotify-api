<?php

namespace Spotify\Tests\Auth;

use Carbon\Carbon;
use Spotify\Auth\State;
use Spotify\Auth\Manager;
use PHPUnit\Framework\TestCase;
use Spotify\Contracts\Store\Session;
use Spotify\Contracts\Auth\Authenticator;

class ManagerTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp() : void
    {
        // Mock Carbon `now()`.
        Carbon::setTestNow(Carbon::create(2019, 9, 1, 12, 0, 0));

        $this->authMock = $this->getMockBuilder(Authenticator::class)
            ->disableOriginalConstructor()
            ->setMethods(['requestAccessToken'])
            ->getMock();

        $this->sessionMock = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->setMethods(['get', 'put'])
            ->getMock();
    }

    /**
     * @return void
     */
    public function test_retrieving_fresh_access_token() : void
    {
        $state = new State('fresh-access-token', 3600);

        $this->authMock->expects($this->once())
            ->method('requestAccessToken')
            ->willReturn($state);

        $manager = new Manager($this->authMock, $this->sessionMock);

        $token = $manager->getAccessToken();

        $this->assertEquals($token, 'fresh-access-token');
    }

    /**
     * @return void
     */
    public function test_retrieving_fresh_access_token_without_session() : void
    {
        $state = new State('fresh-access-token', 3600);

        $this->authMock->expects($this->once())
            ->method('requestAccessToken')
            ->willReturn($state);

        $manager = new Manager($this->authMock);

        $token = $manager->getAccessToken();

        $this->assertEquals($token, 'fresh-access-token');
    }

    /**
     * @return void
     */
    public function test_retrieving_expired_access_token_from_session() : void
    {
        $state = new State('fresh-access-token', 3600);

        $this->authMock->expects($this->once())
            ->method('requestAccessToken')
            ->willReturn($state);

        $this->sessionMock->expects($this->exactly(2))
            ->method('get')
            ->will($this->onConsecutiveCalls(false, false));

        $manager = new Manager($this->authMock, $this->sessionMock);

        $token = $manager->getAccessToken();

        $this->assertEquals($token, 'fresh-access-token');
    }

    /**
     * @return void
     */
    public function test_retrieving_access_token_from_session() : void
    {
        $this->sessionMock->expects($this->exactly(3))
            ->method('get')
            ->will($this->onConsecutiveCalls(
                Carbon::now()->addHour()->timestamp,
                'stored-access-token',
                'stored-access-token'
            ));

        $manager = new Manager($this->authMock, $this->sessionMock);

        $token = $manager->getAccessToken();

        $this->assertEquals($token, 'stored-access-token');
    }
}
