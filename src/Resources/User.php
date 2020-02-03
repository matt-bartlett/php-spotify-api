<?php

namespace Spotify\Resources;

use stdClass;
use Spotify\Constants\Auth;
use Spotify\Constants\Http;
use GuzzleHttp\RequestOptions;

/**
 * Class User
 *
 * @package Spotify\Resources
 */
class User extends Resource
{
    /**
     * Get the profile of the currently authenticated user.
     *
     * @return stdClass
     */
    public function me() : stdClass
    {
        $url = sprintf('%s/me', self::API_BASE_URL);

        $payload = [
            RequestOptions::HEADERS => [
                'Authorization' => sprintf('Bearer %s', $this->getAccessToken(Auth::USER_ENTITY)),
            ]
        ];

        return $this->request->send(Http::GET, $url, $payload);
    }
}
