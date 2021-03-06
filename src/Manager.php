<?php

namespace Spotify;

use Carbon\Carbon;
use Spotify\Auth\State;
use Spotify\Constants\Auth;
use Spotify\Contracts\Store\Session;
use Spotify\Contracts\Auth\Authenticator;
use Spotify\Exceptions\UserRequiresAuthorizationException;

/**
 * Class Manager
 *
 * @package Spotify
 */
class Manager
{
    /**
     * @var \Spotify\Contracts\Auth\Authenticator
     */
    private $authenticator;

    /**
     * @var \Spotify\Contracts\Store\Session
     */
    private $session;

    /**
     * @param \Spotify\Contracts\Auth\Authenticator $authenticator
     * @param \Spotify\Contracts\Store\Session $session
     */
    public function __construct(Authenticator $authenticator, Session $session)
    {
        $this->authenticator = $authenticator;
        $this->session = $session;
    }

    /**
     * Generate the authorization URL for authenticating using the Authorization Flow.
     * https://developer.spotify.com/documentation/general/guides/authorization-guide/#authorization-code-flow
     *
     * @param array $scopes
     * @param bool $showDialog
     *
     * @return string
     */
    public function getAuthorizationUrl(array $scopes, bool $showDialog) : string
    {
        return $this->authenticator->getAuthorizationUrl($scopes, $showDialog);
    }

    /**
     * Handle the callback from the Spotify API. It should exchange the code
     * for an access token and save it to our session.
     *
     * @param string $code
     *
     * @return State
     */
    public function handleCallback(string $code) : State
    {
        $state = $this->authenticator->requestAccessToken($code);

        $this->updateSession(Auth::USER_ENTITY, $state);

        return $state;
    }

    /**
     * Retrieve an access token generated by a respective authentication
     * flow from the session, or request a fresh token.
     *
     * @param string $type
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function getAccessToken(string $type) : string
    {
        switch ($type) {
            case Auth::USER_ENTITY:
                $state = $this->getUserAccessToken();
                break;

            case Auth::CLIENT_ENTITY:
                $state = $this->getClientAccessToken();
                break;

            default:
                throw new \InvalidArgumentException(sprintf('[%s] is an unsupported type.', $type));
        }

        return $state->getAccessToken();
    }

    /**
     * @return State
     */
    private function getClientAccessToken() : State
    {
        $state = $this->session->get(Auth::CLIENT_ENTITY);

        if ($this->isStateValid($state)) {
            return $state;
        }

        // State is invalid, so let's get a new token.
        $state = $this->authenticator->requestCredentialsToken();

        $this->updateSession(Auth::CLIENT_ENTITY, $state);

        return $state;
    }

    /**
     * @return State
     *
     * @throws \Spotify\Exceptions\UserRequiresAuthorizationException
     */
    private function getUserAccessToken() : State
    {
        $state = $this->session->get(Auth::USER_ENTITY);

        // If no state exists in the session, we can
        // assume the user has yet to authorize themselves.
        if (is_null($state)) {
            throw new UserRequiresAuthorizationException;
        }

        if ($this->isStateValid($state)) {
            return $state;
        }

        // After previously requesting a token using the refresh_token,
        // user will need to re-authorize themselves.
        if (is_null($state->getRefreshToken())) {
            throw new UserRequiresAuthorizationException;
        }

        // State is invalid, so let's get a new token.
        $state = $this->authenticator->refreshToken($state->getRefreshToken());

        $this->updateSession(Auth::USER_ENTITY, $state);

        return $state;
    }

    /**
     * Update the access_token stored in the session.
     *
     * @param string $type
     * @param State $state
     *
     * @return void
     */
    private function updateSession(string $type, State $state) : void
    {
        $this->session->put($type, $state);
    }

    /**
     * Check if the current state is valid.
     *
     * @param State|null $state
     *
     * @return bool
     */
    private function isStateValid(?State $state) : bool
    {
        if (is_null($state)) {
            return false;
        }

        $expiresAt = $state->getExpiresAt();
        $accessToken = $state->getAccessToken();

        return
            $expiresAt &&
            $accessToken &&
            Carbon::createFromTimestamp($expiresAt)->gt(Carbon::now());
    }
}
