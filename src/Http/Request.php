<?php

namespace Spotify\Http;

use stdClass;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Spotify\Exceptions\SpotifyRequestException;
use Spotify\Exceptions\AuthenticationException;

/**
 * Class Request
 *
 * @package Spotify\Http
 */
class Request
{
    /**
     * @var \GuzzleHttp\Client
     */
    private $guzzle;

    /**
     * @param \GuzzleHttp\Client $guzzle
     */
    public function __construct(Client $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    /**
     * Make request to the specified endpoint.
     *
     * @param string $method
     * @param string $url
     * @param array $payload
     *
     * @throws \Spotify\Exceptions\SpotifyRequestException
     * @throws \Spotify\Exceptions\AuthenticationException
     *
     * @return stdClass
     */
    public function send(string $method, string $url, array $payload) : stdClass
    {
        try {
            $response = $this->guzzle
                ->request($method, $url, $payload);
        } catch (ClientException $e) {
            switch ($e->getCode()) {
                case 401:
                    throw new AuthenticationException;
                default:
                    throw new SpotifyRequestException($url, $e->getCode(), $e);
            }
        }

        return json_decode($response->getBody());
    }
}
