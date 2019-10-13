<?php

namespace Spotify\Auth;

use Carbon\Carbon;

/**
 * Class State
 *
 * @package Spotify\Auth
 */
class State
{
    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var int
     */
    private $expiresAt;

    /**
     * @var int
     */
    private $expiresIn;

    /**
     * @param string $accessToken
     * @param int $expiresIn
     */
    public function __construct(string $accessToken, int $expiresIn)
    {
        $this->expiresIn = $expiresIn;
        $this->accessToken = $accessToken;
        $this->calculateTokenExpiry();
    }

    /**
     * @return string
     */
    public function getAccessToken() : string
    {
        return $this->accessToken;
    }

    /**
     * @return int
     */
    public function getExpiresAt() : int
    {
        return $this->expiresAt;
    }

    /**
     * @return void
     */
    private function calculateTokenExpiry() : void
    {
        $expiresAt = Carbon::now()
            ->addSeconds($this->expiresIn)
            ->timestamp;

        $this->expiresAt = $expiresAt;
    }
}
