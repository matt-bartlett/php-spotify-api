<?php

namespace Spotify\Contracts\Auth;

use Spotify\Auth\State;

interface Authenticator
{
    /**
     * Request an access token.
     *
     * @return \Spotify\Auth\State
     */
    public function requestAccessToken() : State;
}
