<?php

namespace Spotify\Auth;

use Carbon\Carbon;
use Spotify\Auth\State;
use Spotify\Contracts\Store\Session;
use Spotify\Contracts\Auth\Authenticator;

/**
 * Class Manager
 *
 * @package Spotify\Auth
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
     * @param \Spotify\Contracts\Store\Session|null $session
     */
    public function __construct(Authenticator $authenticator, Session $session = null)
    {
        $this->authenticator = $authenticator;
        $this->session = $session;
    }

    /**
     * Retrieve a valid access token from the session,
     * or generate a fresh access token.
     *
     * @return string
     */
    public function getAccessToken() : string
    {
        if ($this->isTokenValid()) {
            return $this->session->get('access_token');
        }

        $state = $this->authenticator->requestAccessToken();

        $this->updateSession($state);

        return $state->getAccessToken();
    }

    /**
     * Update the access_token stored in the session.
     *
     * @param State $state
     *
     * @return void
     */
    private function updateSession(State $state) : void
    {
        if (!is_null($this->session)) {
            $this->session->put([
                'expires_at' => $state->getExpiresAt(),
                'access_token' => $state->getAccessToken()
            ]);
        }
    }

    /**
     * Check if the current state is valid.
     *
     * @return void
     */
    private function isTokenValid() : bool
    {
        // Return early if no session is being used.
        if (is_null($this->session)) {
            return false;
        }

        $expiresAt = $this->session->get('expires_at', false);
        $accessToken = $this->session->get('access_token', false);

        return
            $expiresAt &&
            $accessToken &&
            Carbon::createFromTimestamp($expiresAt)->gt(Carbon::now());
    }
}
