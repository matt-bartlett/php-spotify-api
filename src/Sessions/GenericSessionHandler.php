<?php

namespace Spotify\Sessions;

use Spotify\Auth\State;
use Spotify\Contracts\Store\Session;

/**
 * Class GenericSessionHandler
 *
 * @package Spotify\Sessions
 */
class GenericSessionHandler implements Session
{
    /**
     * @var string
     */
    private const SESSION_KEY_PREFIX = 'spotify_session';

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            throw new \RuntimeException('Session has not started.');
        }
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

        return $_SESSION[$key] ?? $default;
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

        $_SESSION[$key] = $data;
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

        unset($_SESSION[$key]);
    }
}
