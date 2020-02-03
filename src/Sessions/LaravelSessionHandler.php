<?php

namespace Spotify\Sessions;

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
        return $this->session->get($key, $default);
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
        $this->session->put($data);
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
        $this->session->forget($key);
    }
}
