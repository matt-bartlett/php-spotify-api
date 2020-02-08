<?php

namespace Spotify\Auth;

use Spotify\Auth\State;
use Spotify\Http\Request;
use Spotify\Auth\Credentials;
use Spotify\Constants\Auth;
use Spotify\Constants\Http;
use Spotify\Constants\Grant;
use GuzzleHttp\RequestOptions;
use Spotify\Contracts\Auth\Authenticator as AuthInterface;

/**
 * Class Authenticator
 *
 * @package Spotify\Auth
 */
class Authenticator implements AuthInterface
{
    /**
     * @var string
     */
    private const TOKEN_URL = 'https://accounts.spotify.com/api/token';

    /**
     * @var string
     */
    private const AUTHORIZATION_URL = 'https://accounts.spotify.com/authorize';

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
     * Generate the authorization URL for authenticating using the Authorization Flow.
     *
     * @param array $scopes
     * @param bool $showDialog
     *
     * @return string
     */
    public function getAuthorizationUrl(array $scopes, bool $showDialog) : string
    {
        // Concatenate scopes.
        $scopes = isset($scopes) ? implode(' ', $scopes) : null;

        // Convert boolean to string for the authorization request.
        $force = ($showDialog) ? 'true' : 'false';

        $parameters = [
            'scope' => $scopes,
            'client_id' => $this->credentials->getClientId(),
            'show_dialog' => $force,
            'redirect_uri' => $this->credentials->getRedirectUrl(),
            'response_type' => 'code',
        ];

        return sprintf('%s?%s', self::AUTHORIZATION_URL, http_build_query($parameters));
    }

    /**
     * Authenticate with the Authorization Flow, using the code returned by Spotify.
     *
     * @param string $code
     *
     * @return \Spotify\Auth\State
     */
    public function requestAccessToken(string $code) : State
    {
        $payload = array_merge($this->getDefaultHeaders(), [
            RequestOptions::FORM_PARAMS => [
                'code' => $code,
                'grant_type' => Grant::AUTHORIZATION_CODE,
                'redirect_uri' => $this->credentials->getRedirectUrl(),
            ]
        ]);

        $response = $this->request->send(Http::POST, self::TOKEN_URL, $payload);

        return new State(
            Auth::USER_ENTITY,
            $response->access_token,
            $response->expires_in,
            $response->refresh_token
        );
    }

    /**
     * Authenticate with Spotify using the Client Credentials Flow.
     *
     * @return \Spotify\Auth\State
     */
    public function requestCredentialsToken() : State
    {
        $payload = array_merge($this->getDefaultHeaders(), [
            RequestOptions::FORM_PARAMS => [
                'grant_type' => Grant::CLIENT_CREDENTIALS,
            ]
        ]);

        $response = $this->request->send(Http::POST, self::TOKEN_URL, $payload);

        return new State(
            Auth::CLIENT_ENTITY,
            $response->access_token,
            $response->expires_in
        );
    }

    /**
     * Refresh an access token generated by the Authorization Flow.
     *
     * @param string $refreshToken
     *
     * @return \Spotify\Auth\State
     */
    public function refreshToken(string $refreshToken) : State
    {
        $payload = array_merge($this->getDefaultHeaders(), [
            RequestOptions::FORM_PARAMS => [
                'grant_type' => Grant::REFRESH_TOKEN,
                'refresh_token' => $refreshToken,
            ]
        ]);

        $response = $this->request->send(Http::POST, self::TOKEN_URL, $payload);

        return new State(
            Auth::USER_ENTITY,
            $response->access_token,
            $response->expires_in,
            $response->refresh_token
        );
    }

    /**
     * Encode credentials attached to request header.
     *
     * @return string
     */
    private function getDefaultHeaders()
    {
        $token = base64_encode(sprintf(
            '%s:%s',
            $this->credentials->getClientId(),
            $this->credentials->getClientSecret()
        ));

        return [
            RequestOptions::HEADERS => [
                'Authorization' => sprintf('Basic %s', $token),
            ]
        ];
    }
}
