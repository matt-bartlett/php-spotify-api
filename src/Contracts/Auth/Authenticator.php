<?php

namespace Spotify\Contracts\Auth;

use Spotify\Auth\State;

interface Authenticator
{
    /**
     * Generate the authorization URL for authenticating using the Authorization Flow.
     *
     * @param array $scopes
     * @param bool $showDialog
     *
     * @return string
     */
    public function getAuthorizationUrl(array $scopes, bool $showDialog) : string;

    /**
     * Authenticate with the Authorization Flow, using the code returned by Spotify.
     *
     * @param string $code
     *
     * @return \Spotify\Auth\State
     */
    public function requestAccessToken(string $code) : State;

    /**
     * Authenticate with the Client Credentials Flow
     *
     * @return \Spotify\Auth\State
     */
    public function requestCredentialsToken() : State;

    /**
     * Refresh an access token generated by the Authorization Flow.
     *
     * @param string $refreshToken
     *
     * @return \Spotify\Auth\State
     */
    public function refreshToken(string $refreshToken) : State;
}
