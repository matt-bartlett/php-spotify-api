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
     * @param array $data
     *
     * @return void
     */
    public function put(array $data) : void
    {
        $this->session->put($data);
    }
}
