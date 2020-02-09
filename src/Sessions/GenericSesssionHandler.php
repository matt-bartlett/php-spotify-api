<?php

namespace Spotify\Sessions;

use Spotify\Contracts\Store\Session;
use Illuminate\Contracts\Session\Session as LaravelSession;

/**
 * Class GenericSessionHandler
 *
 * @package Spotify\Sessions
 */
class GenericSessionHandler implements Session
{
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
        return (isset($_SESSION[$key])) ? $_SESSION[$key] : $default;
    }

    /**
     * Add data to the session.
     *
     * @param array $data
     *
     * @return void
     */
    public function put(array $data) : void
    {
        $key = array_keys($data);
        $value = $data[$key];

        $_SESSION[$key] = $value;
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
        unset($_SESSION[$key]);
    }
}
