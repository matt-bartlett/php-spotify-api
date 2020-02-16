<?php

namespace Spotify\Tests;

use Carbon\Carbon;
use Spotify\Manager;
use Spotify\Auth\State;
use Spotify\Constants\Auth;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Spotify\Contracts\Store\Session;
use Spotify\Contracts\Auth\Authenticator;
use Spotify\Exceptions\UserRequiresAuthorizationException;

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
            ->setMethods([
                'refreshToken',
                'getAuthorizationUrl',
                'requestAccessToken',
                'requestCredentialsToken',
            ])
            ->getMock();

        $this->sessionMock = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->setMethods(['get', 'put', 'forget'])
            ->getMock();
    }

    /**
     * @return void
     */
    public function test_client_retrieving_fresh_access_token() : void
    {
        $state = new State(Auth::CLIENT_ENTITY, 'fresh-access-token', 3600);

        $this->authMock->expects($this->once())
            ->method('requestCredentialsToken')
            ->willReturn($state);

        $manager = new Manager($this->authMock, $this->sessionMock);

        $token = $manager->getAccessToken(Auth::CLIENT_ENTITY);

        $this->assertEquals($token, 'fresh-access-token');
    }

    /**
     * @return void
     */
    public function test_client_retrieving_expired_access_token_from_session() : void
    {
        $state = new State(Auth::CLIENT_ENTITY, 'access-token', 3600);

        $this->sessionMock->expects($this->once())
            ->method('get')
            ->willReturn($state);

        // Set Carbon 2 hours ahead.
        Carbon::setTestNow(Carbon::create(2019, 9, 1, 14, 0, 0));

        $state = new State(Auth::USER_ENTITY, 'new-access-token', 3600);

        $this->authMock->expects($this->once())
            ->method('requestCredentialsToken')
            ->willReturn($state);

        $manager = new Manager($this->authMock, $this->sessionMock);

        $token = $manager->getAccessToken(Auth::CLIENT_ENTITY);

        $this->assertEquals($token, 'new-access-token');
    }

    /**
     * @return void
     */
    public function test_client_retrieving_access_token_from_session() : void
    {
        $state = new State(Auth::CLIENT_ENTITY, 'stored-access-token', 3600);

        $this->sessionMock->expects($this->once())
            ->method('get')
            ->willReturn($state);

        $manager = new Manager($this->authMock, $this->sessionMock);

        $token = $manager->getAccessToken(Auth::CLIENT_ENTITY);

        $this->assertEquals($token, 'stored-access-token');
    }

    /**
     * @return void
     */
    public function test_authorization_url_generation() : void
    {
        $this->authMock->expects($this->once())
            ->method('getAuthorizationUrl')
            ->willReturn('https://accounts.spotify.com/authorize/random-url');

        $manager = new Manager($this->authMock, $this->sessionMock);

        $url = $manager->getAuthorizationUrl([], true);

        $this->assertEquals($url, 'https://accounts.spotify.com/authorize/random-url');
    }

    /**
     * @return void
     */
    public function test_callback_handler() : void
    {
        $state = new State(Auth::USER_ENTITY, 'access-token', 3600, 'refresh-token');

        $this->authMock->expects($this->once())
            ->method('requestAccessToken')
            ->willReturn($state);

        $manager = new Manager($this->authMock, $this->sessionMock);

        $state = $manager->handleCallback('random-code');

        $this->assertEquals($state->getType(), Auth::USER_ENTITY);
        $this->assertEquals($state->getAccessToken(), 'access-token');
        $this->assertEquals($state->getRefreshToken(), 'refresh-token');
    }

    /**
     * @return void
     */
    public function test_user_retrieving_access_token_with_invalid_type() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('[invalid-type] is an unsupported type.');

        $manager = new Manager($this->authMock, $this->sessionMock);

        $manager->getAccessToken('invalid-type');
    }

    /**
     * @return void
     */
    public function test_user_retrieving_access_token_without_authorizing_fails() : void
    {
        $this->expectException(UserRequiresAuthorizationException::class);
        $this->expectExceptionMessage('User needs to authorize.');

        $manager = new Manager($this->authMock, $this->sessionMock);

        $manager->getAccessToken(Auth::USER_ENTITY);
    }

    /**
     * @return void
     */
    public function test_user_retrieving_access_token_from_session() : void
    {
        $state = new State(Auth::USER_ENTITY, 'access-token', 3600, 'refresh-token');

        $this->sessionMock->expects($this->once())
            ->method('get')
            ->willReturn($state);

        $manager = new Manager($this->authMock, $this->sessionMock);

        $token = $manager->getAccessToken(Auth::USER_ENTITY);

        $this->assertEquals($token, 'access-token');
    }

    /**
     * @return void
     */
    public function test_user_refreshes_expired_access_token() : void
    {
        $state = new State(Auth::USER_ENTITY, 'access-token', 3600, 'refresh-token');

        $this->sessionMock->expects($this->once())
            ->method('get')
            ->willReturn($state);

        // Set Carbon 2 hours ahead.
        Carbon::setTestNow(Carbon::create(2019, 9, 1, 14, 0, 0));

        $state = new State(Auth::USER_ENTITY, 'new-access-token', 3600, 'new-refresh-token');

        $this->authMock->expects($this->once())
            ->method('refreshToken')
            ->willReturn($state);

        $manager = new Manager($this->authMock, $this->sessionMock);

        $token = $manager->getAccessToken(Auth::USER_ENTITY);

        $this->assertEquals($token, 'new-access-token');
    }

    /**
     * @return void
     */
    public function test_refreshing_token_fails_having_already_refreshed() : void
    {
        $this->expectException(UserRequiresAuthorizationException::class);
        $this->expectExceptionMessage('User needs to authorize.');

        $state = new State(Auth::USER_ENTITY, 'access-token', 3600);

        $this->sessionMock->expects($this->once())
            ->method('get')
            ->willReturn($state);

        // Set Carbon 2 hours ahead.
        Carbon::setTestNow(Carbon::create(2019, 9, 1, 14, 0, 0));

        $manager = new Manager($this->authMock, $this->sessionMock);

        $manager->getAccessToken(Auth::USER_ENTITY);
    }
}
