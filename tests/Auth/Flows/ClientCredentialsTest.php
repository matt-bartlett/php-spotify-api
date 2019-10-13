<?php

namespace Spotify\Tests\Auth\Flows;

use stdClass;
use Spotify\Auth\State;
use Spotify\Http\Request;
use Spotify\Auth\Credentials;
use PHPUnit\Framework\TestCase;
use Spotify\Auth\Flows\ClientCredentials;

class ClientCredentialsTest extends TestCase
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

        $this->clientCredentials = new ClientCredentials(
            $this->requestMock,
            $this->credentials
        );
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

        $response = $this->clientCredentials->requestAccessToken();

        $this->assertInstanceOf(State::class, $response);
        $this->assertEquals($response->getAccessToken(), 'access-token');
    }
}
