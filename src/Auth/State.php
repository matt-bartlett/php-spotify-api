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
    private $type;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var string
     */
    private $refreshToken;

    /**
     * @var int
     */
    private $expiresAt;

    /**
     * @var int
     */
    private $expiresIn;

    /**
     * @param string $type
     * @param string $accessToken
     * @param int $expiresIn
     * @param string|null $refreshToken
     */
    public function __construct(string $type, string $accessToken, int $expiresIn, string $refreshToken = null)
    {
        $this->type = $type;
        $this->expiresIn = $expiresIn;
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
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
     * @return string
     */
    public function getRefreshToken() : ?string
    {
        return $this->refreshToken;
    }

    /**
     * @return int
     */
    public function getExpiresAt() : int
    {
        return $this->expiresAt;
    }

    /**
     * @return array
     */
    public function toArray() : array
    {
        return [
            'expires_at' => $this->getExpiresAt(),
            'access_token' => $this->getAccessToken(),
            'resfresh_token' => $this->getRefreshToken(),
        ];
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
