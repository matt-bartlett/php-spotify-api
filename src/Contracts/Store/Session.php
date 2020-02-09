<?php

namespace Spotify\Contracts\Store;

interface Session
{
    /**
     * Retrieve a value from the session matching the reference key.
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Add data to the session.
     *
     * @param array $data
     *
     * @return void
     */
    public function put(array $data) : void;

    /**
     * Remove data from the session.
     *
     * @param string $key
     *
     * @return void
     */
    public function forget(string $key) : void;
}
