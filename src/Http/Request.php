<?php

namespace Spotify\Http;

use stdClass;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\TransferException;
use Spotify\Exceptions\SpotifyRequestException;
use Spotify\Exceptions\AuthenticationException;

/**
 * Class Request
 *
 * @package Spotify\Http
 */
class Request
{
    private const METHOD_GET = 'GET';

    private const METHOD_POST = 'POST';

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
     * Send GET request using the Guzzle HTTP Client
     *
     * @param string $url
     * @param array $headers
     * @param array $parameters
     *
     * @return stdClass
     */
    public function get(string $url, array $headers = [], array $parameters = []) : stdClass
    {
        $url = sprintf('%s?%s', $url, http_build_query($parameters));

        try {
            $response = $this->guzzle->request(self::METHOD_GET, $url, [
                'headers' => array_merge(
                    $this->getDefaultHeaders(self::METHOD_GET),
                    $headers
                ),
            ]);
        } catch (ClientException $e) {
            switch ($e->getCode()) {
                case 401:
                    throw new AuthenticationException;
                default:
                    throw new SpotifyRequestException(
                        $e->getMessage(),
                        $e->getCode()
                    );
            }
        }

        return json_decode($response->getBody());
    }

    /**
     * Send POST request using the Guzzle HTTP Client
     *
     * @param string $url
     * @param array $headers
     * @param array $parameters
     *
     * @return stdClass
     *
     * @throws AuthenticationException
     * @throws SpotifyRequestException
     */
    public function post(string $url, array $headers = [], array $parameters = []) : stdClass
    {
        try {
            $response = $this->guzzle->request(self::METHOD_POST, $url, [
                'headers' => array_merge(
                    $this->getDefaultHeaders(self::METHOD_POST),
                    $headers
                ),
                'form_params' => $parameters,
            ]);
        } catch (ClientException $e) {
            switch ($e->getCode()) {
                case 401:
                    throw new AuthenticationException;
                default:
                    throw new SpotifyRequestException(
                        $e->getMessage(),
                        $e->getCode()
                    );
            }
        }

        return json_decode($response->getBody());
    }

    /**
     * @param string $method
     *
     * @return array
     */
    private function getDefaultHeaders(string $method) : array
    {
        $headers = [];

        switch ($method) {
            case self::METHOD_GET:
                $headers = [
                    'Accepts' => 'application/json',
                    'Content-Type' => 'application/json',
                ];
                break;
            case self::METHOD_POST:
                $headers = [
                    'Accepts' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ];
                break;
        }

        return $headers;
    }
}
