<?php

namespace Spotify\Auth\Flows;

use Spotify\Auth\State;
use Spotify\Http\Request;
use Spotify\Auth\Credentials;
use Spotify\Contracts\Auth\Authenticator;

/**
 * Class ClientCredentials
 *
 * @package Spotify\Auth\Flows
 */
class ClientCredentials implements Authenticator
{
    private const AUTH_URL = 'https://accounts.spotify.com/api/token';

    /**
     * @var \Spotify\Http\Request
     */
    private $request;

    /**
     * @var \Spotify\Auth\Credentials
     */
    private $credentials;

    /**
     * @param \Spotify\Http\Request $request
     * @param \Spotify\Auth\Credentials $credentials
     */
    public function __construct(Request $request, Credentials $credentials)
    {
        $this->request = $request;
        $this->credentials = $credentials;
    }

    /**
     * Authenticate with Spotify using the Client Credentials Flow
     *
     * @return \Spotify\Auth\State
     */
    public function requestAccessToken() : State
    {
        // Encode the app Client ID & Client Secret for authorization.
        $token = base64_encode(sprintf(
            '%s:%s',
            $this->credentials->getClientId(),
            $this->credentials->getClientSecret()
        ));

        // Set headers.
        $headers = [
            'Authorization' => sprintf('Basic %s', $token),
        ];

        // Set POST params.
        $parameters = [
            'grant_type' => 'client_credentials'
        ];

        // Make request for access token.
        $response = $this->request->post(self::AUTH_URL, $headers, $parameters);

        return new State(
            $response->access_token,
            $response->expires_in
        );
    }
}
