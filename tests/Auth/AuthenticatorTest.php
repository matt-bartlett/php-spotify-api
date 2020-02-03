<?php

namespace Spotify\Tests\Auth;

use stdClass;
use Spotify\Auth\State;
use Spotify\Http\Request;
use Spotify\Constants\Auth;
use Spotify\Constants\Scope;
use Spotify\Auth\Credentials;
use Spotify\Auth\Authenticator;
use PHPUnit\Framework\TestCase;

class AuthenticatorTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp() : void
    {
        $this->requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['post'])
            ->getMock();

        $this->credentials = new Credentials(
            'client-id',
            'client-secret',
            'redirect-url'
        );

        $this->authenticator = new Authenticator(
            $this->requestMock,
            $this->credentials
        );
    }

    /**
     * @return void
     */
    public function test_authorization_url_generation() : void
    {
        $scopes = [Scope::USER_READ_EMAIL];

        $url = $this->authenticator->getAuthorizationUrl($scopes, false);

        $expected = "https://accounts.spotify.com/authorize?scope=user-read-email&client_id=client-id&show_dialog=0&redirect_uri=redirect-url&response_type=code";

        $this->assertEquals($url, $expected);
    }

    /**
     * @return void
     */
    public function test_client_can_request_access_token() : void
    {
        $result = new stdClass;
        $result->expires_in = 3600;
        $result->access_token = 'access-token';

        $this->requestMock->expects($this->once())
            ->method('post')
            ->willReturn($result);

        $state = $this->authenticator->requestCredentialsToken();

        $this->assertInstanceOf(State::class, $state);
        $this->assertEquals($state->getType(), Auth::CLIENT_ENTITY);
        $this->assertEquals($state->getAccessToken(), 'access-token');
    }

    /**
     * @return void
     */
    public function test_user_can_request_access_token() : void
    {
        $result = new stdClass;
        $result->expires_in = 3600;
        $result->access_token = 'access-token';
        $result->refresh_token = 'refresh-token';

        $this->requestMock->expects($this->once())
            ->method('post')
            ->willReturn($result);

        $state = $this->authenticator->requestAccessToken('random-code');

        $this->assertInstanceOf(State::class, $state);
        $this->assertEquals($state->getType(), Auth::USER_ENTITY);
        $this->assertEquals($state->getAccessToken(), 'access-token');
    }

    /**
     * @return void
     */
    public function test_user_can_refresh_access_token() : void
    {
        $result = new stdClass;
        $result->expires_in = 3600;
        $result->access_token = 'new-access-token';
        $result->refresh_token = 'new-refresh-token';

        $this->requestMock->expects($this->once())
            ->method('post')
            ->willReturn($result);

        $state = $this->authenticator->refreshToken('refresh-token');

        $this->assertInstanceOf(State::class, $state);
        $this->assertEquals($state->getType(), Auth::USER_ENTITY);
        $this->assertEquals($state->getAccessToken(), 'new-access-token');
        $this->assertEquals($state->getRefreshToken(), 'new-refresh-token');
    }
}
