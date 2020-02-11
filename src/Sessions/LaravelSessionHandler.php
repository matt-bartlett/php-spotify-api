<?php

namespace Spotify\Sessions;

use Spotify\Auth\State;
use Spotify\Contracts\Store\Session;
use Illuminate\Contracts\Session\Session as LaravelSession;

/**
 * Class LaravelSessionHandler
 *
 * @package Spotify\Sessions
 */
class LaravelSessionHandler implements Session
{
    /**
     * @var string
     */
    private const SESSION_KEY_PREFIX = 'spotify_session';

    /**
     * @var \Illuminate\Contracts\Session\Session
     */
    private $session;

    /**
     * @param \Illuminate\Contracts\Session\Session $session
     */
    public function __construct(LaravelSession $session)
    {
        $this->session = $session;
    }

    /**
     * Retrieve a value from the session matching the reference key.
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $key = sprintf('%s_%s', self::SESSION_KEY_PREFIX, $key);

        return $this->session->get($key, $default);
    }

    /**
     * Add data to the session.
     *
     * @param string $key
     * @param State $data
     *
     * @return void
     */
    public function put(string $key, State $data) : void
    {
        $key = sprintf('%s_%s', self::SESSION_KEY_PREFIX, $key);

        $this->session->put([
            $key => $data
        ]);
    }

    /**
     * Remove data from the session.
     *
     * @param string $key
     *
     * @return void
     */
    public function forget(string $key) : void
    {
        $key = sprintf('%s_%s', self::SESSION_KEY_PREFIX, $key);

        $this->session->forget($key);
    }
}
