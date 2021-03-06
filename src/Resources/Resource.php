<?php

namespace Spotify\Resources;

use stdClass;
use Spotify\Manager;
use Spotify\Http\Request;
use Spotify\Constants\Auth;
use Spotify\Constants\Http;
use GuzzleHttp\RequestOptions;

/**
 * Class Resource
 *
 * @package Spotify\Resources
 */
abstract class Resource
{
    protected const API_BASE_URL = 'https://api.spotify.com/v1';

    /**
     * @var \Spotify\Http\Request
     */
    protected $request;

    /**
     * @var \Spotify\Manager
     */
    protected $manager;

    /**
     * @param \Spotify\Http\Request $request
     * @param \Spotify\Manager $manager
     */
    public function __construct(Request $request, Manager $manager)
    {
        $this->manager = $manager;
        $this->request = $request;
    }

    /**
     * Fetch the Auth Manager.
     *
     * @return \Spotify\Manager
     */
    public function getManager() : Manager
    {
        return $this->manager;
    }

    /**
     * Get the profile of the currently authenticated user.
     *
     * @return stdClass
     */
    protected function getUserProfile() : stdClass
    {
        $url = sprintf('%s/me', self::API_BASE_URL);

        $payload = [
            RequestOptions::HEADERS => [
                'Authorization' => sprintf('Bearer %s', $this->getManager()->getAccessToken(Auth::USER_ENTITY)),
            ]
        ];

        return $this->request->send(Http::GET, $url, $payload);
    }
}
