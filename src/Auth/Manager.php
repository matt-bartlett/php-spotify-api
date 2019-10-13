<?php

namespace Spotify\Auth;

use Carbon\Carbon;
use Spotify\Auth\State;
use SessionHandlerInterface;
use Illuminate\Session\SessionManager;
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
     * @var \Illuminate\Session\SessionManager
     *
     * @todo Replace with \SessionHandlerInterface
     */
    private $session;

    /**
     * @param \Spotify\Contracts\Auth\Authenticator $authenticator
     * @param \Illuminate\Session\SessionManager    $session
     */
    public function __construct(Authenticator $authenticator, SessionManager $session)
    {
        $this->authenticator = $authenticator;
        $this->session = $session;
    }

    /**
     * Retrieve the access token from the session,
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
     * Update the access_token stored in the session
     * and extend the expiration time.
     *
     * @param State $state
     *
     * @return void
     */
    private function updateSession(State $state) : void
    {
        $this->session->put([
            'expires_at' => $state->getExpiresAt(),
            'access_token' => $state->getAccessToken()
        ]);
    }

    /**
     * Check if the current state is valid
     *
     * @return void
     */
    private function isTokenValid() : bool
    {
        $expiresAt = $this->session->get('expires_at', false);
        $accessToken = $this->session->get('access_token', false);

        return
            $expiresAt &&
            $accessToken &&
            Carbon::createFromTimestamp($expiresAt)->gt(Carbon::now());
    }
}
