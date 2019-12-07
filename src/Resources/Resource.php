<?php

namespace Spotify\Resources;

use Spotify\Http\Request;
use Spotify\Auth\Manager;

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
     * @return string
     */
    protected function getAccessToken() : string
    {
        return $this->manager->getAccessToken();
    }
}
