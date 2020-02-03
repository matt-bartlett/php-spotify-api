<?php

namespace Spotify\Resources;

use stdClass;
use Spotify\Http\Request;
use Spotify\Auth\Manager;
use Spotify\Constants\Auth;

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
     * @var \Spotify\Auth\Manager
     */
    protected $manager;

    /**
     * @param \Spotify\Http\Request $request
     * @param \Spotify\Auth\Manager $manager
     */
    public function __construct(Request $request, Manager $manager)
    {
        $this->manager = $manager;
        $this->request = $request;
    }

    /**
     * Fetch an access token from the Auth Manager.
     *
     * @param string $type
     *
     * @return string
     */
    protected function getAccessToken(string $type) : string
    {
        return $this->manager->getAccessToken($type);
    }

    /**
     * Get the profile of the currently authenticated user.
     *
     * @return stdClass
     */
    protected function getUserProfile() : stdClass
    {
        $url = sprintf('%s/me', self::API_BASE_URL);

        return $this->request->get($url, [
            'Authorization' => sprintf('Bearer %s', $this->getAccessToken(Auth::USER_ENTITY)),
        ]);
    }
}
