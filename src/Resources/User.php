<?php

namespace Spotify\Resources;

use stdClass;
use Spotify\Constants\Auth;

/**
 * Class User
 *
 * @package Spotify\Resources
 */
class User extends Resource
{
    /**
     * ...
     *
     * @return stdClass
     */
    public function me() : stdClass
    {
        $url = sprintf('%s/me', self::API_BASE_URL);

        return $this->request->get($url, [
            'Authorization' => sprintf('Bearer %s', $this->getAccessToken(Auth::USER_ENTITY)),
        ]);
    }
}
