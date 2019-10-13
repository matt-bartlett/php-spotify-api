<?php

namespace Spotify\Tests\Http;

use stdClass;
use GuzzleHttp\Client;
use Spotify\Http\Request;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Exception\ClientException;
use Spotify\Exceptions\SpotifyRequestException;
use Spotify\Exceptions\AuthenticationException;
use GuzzleHttp\Psr7\Request as GuzzlePsrRequest;
use GuzzleHttp\Psr7\Response as GuzzlePsrResponse;

class RequestTest extends TestCase
{
    /**
     * @var \Spotify\Http\Request
     * @var \GuzzleHttp\Client
     * @var \GuzzleHttp\Psr7\Response
     */
    private $request;
    private $guzzleClientMock;
    private $guzzleResponseMock;

    private const TEST_URL = 'https://api.spotify.com/v1/playlist/1';

    /**
     * @return void
     */
    public function setUp()
    {
        // Guzzle Client mock.
        $this->guzzleClientMock = $this->getMockBuilder(Client::class)
            ->setMethods(['request'])
            ->getMock();

        // Guzzle PSR-7 Response mock.
        $this->guzzleResponseMock = $this->getMockBuilder(GuzzlePsrResponse::class)
            ->setMethods(['getBody'])
            ->getMock();

        // Instantiate Request class.
        $this->request = new Request($this->guzzleClientMock);

        parent::setUp();
    }

    /**
     * @return void
     */
    public function test_get_request_is_successful()
    {
        $expected = new stdClass;
        $expected->name = 'Test Playlist';

        $this->guzzleResponseMock->expects($this->once())
            ->method('getBody')
            ->willReturn(json_encode($expected));

        $this->guzzleClientMock->expects($this->once())
            ->method('request')
            ->willReturn($this->guzzleResponseMock);

        $response = $this->request->get(self::TEST_URL, ['foo' => 'bar'], ['foo' => 'bar']);

        $this->assertEquals($response, $expected);
    }

    /**
     * @return void
     */
    public function test_post_request_is_successful()
    {
        $expected = (new stdClass);
        $expected->name = 'Test Playlist';

        $this->guzzleResponseMock->expects($this->once())
            ->method('getBody')
            ->willReturn(json_encode($expected));

        $this->guzzleClientMock->expects($this->once())
            ->method('request')
            ->willReturn($this->guzzleResponseMock);

        $response = $this->request->post(self::TEST_URL, ['foo' => 'bar'], ['foo' => 'bar']);

        $this->assertEquals($response, $expected);
    }

    /**
     * @return void
     */
    public function test_request_throws_authentication_exception()
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Your access token is invalid or has expired.');

        $clientExceptionMock = $this->getMockBuilder(ClientException::class)
            ->setConstructorArgs([
                'Error',
                new GuzzlePsrRequest('POST', self::TEST_URL),
                new GuzzlePsrResponse(401)
            ])
            ->getMock();

        $this->guzzleClientMock->expects($this->once())
            ->method('request')
            ->will($this->throwException($clientExceptionMock));

        $this->request->post(self::TEST_URL);
    }

    /**
     * @return void
     */
    public function test_exception_is_thrown_with_message()
    {
        $this->expectException(SpotifyRequestException::class);
        $this->expectExceptionMessage('Your request failed validation.');

        $clientExceptionMock = $this->getMockBuilder(ClientException::class)
            ->setConstructorArgs([
                'Your request failed validation.',
                new GuzzlePsrRequest('POST', self::TEST_URL),
                new GuzzlePsrResponse(400)
            ])
            ->getMock();

        $this->guzzleClientMock->expects($this->once())
            ->method('request')
            ->will($this->throwException($clientExceptionMock));

        $this->request->post(self::TEST_URL);
    }
}
