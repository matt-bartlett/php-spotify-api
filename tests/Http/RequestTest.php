<?php

namespace Spotify\Tests\Http;

use stdClass;
use GuzzleHttp\Client;
use Spotify\Http\Request;
use Spotify\Constants\Http;
use GuzzleHttp\HandlerStack;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use Spotify\Exceptions\SpotifyRequestException;
use Spotify\Exceptions\AuthenticationException;
use GuzzleHttp\Psr7\Response as GuzzlePsrResponse;

class RequestTest extends TestCase
{
    private const TEST_URL = 'https://api.spotify.com/v1/playlist/1';

    /**
     * @return void
     */
    public function test_get_request_is_successful() : void
    {
        $expected = new stdClass;
        $expected->name = 'Test Playlist';

        $request = $this->createRequest(200, json_encode($expected));

        $response = $request->send(Http::GET, self::TEST_URL, []);

        $this->assertEquals($response, $expected);
    }

    /**
     * @return void
     */
    public function test_post_request_is_successful() : void
    {
        $expected = new stdClass;
        $expected->name = 'Test Playlist';

        $request = $this->createRequest(200, json_encode($expected));

        $response = $request->send(Http::POST, self::TEST_URL, ['foo' => 'bar']);

        $this->assertEquals($response, $expected);
    }

    /**
     * @return void
     */
    public function test_get_request_throws_authentication_exception() : void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Your access token is invalid or has expired.');

        $request = $this->createRequest(401);

        $request->send(Http::GET, self::TEST_URL, []);
    }

    /**
     * @return void
     */
    public function test_post_request_throws_authentication_exception() : void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Your access token is invalid or has expired.');

        $request = $this->createRequest(401);

        $request->send(Http::POST, self::TEST_URL, []);
    }

    /**
     * @return void
     */
    public function test_get_exception_is_thrown_with_message() : void
    {
        $this->expectException(SpotifyRequestException::class);
        $this->expectExceptionMessage('API call to [https://api.spotify.com/v1/playlist/1] has failed.');

        $request = $this->createRequest(400);

        $request->send(Http::GET, self::TEST_URL, []);
    }

    /**
     * @return void
     */
    public function test_post_exception_is_thrown_with_message() : void
    {
        $this->expectException(SpotifyRequestException::class);
        $this->expectExceptionMessage('API call to [https://api.spotify.com/v1/playlist/1] has failed.');

        $request = $this->createRequest(400);

        $request->send(Http::POST, self::TEST_URL, []);
    }

    /**
     * Helper method to bind a mock HTTP client to the Request class.
     *
     * @param int $status
     * @param string $body
     *
     * @return Request
     */
    private function createRequest(int $status, string $body = null) : Request
    {
        $mockHandler = new MockHandler([
            new GuzzlePsrResponse($status, [], $body)
        ]);

        $handlerStack = HandlerStack::create($mockHandler);

        $client = new Client(['handler' => $handlerStack]);

        return new Request($client);
    }
}
