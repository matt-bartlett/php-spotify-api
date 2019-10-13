<?php

namespace Spotify\Tests\Auth;

use Carbon\Carbon;
use Spotify\Auth\State;
use PHPUnit\Framework\TestCase;

class StateTest extends TestCase
{
    private $expiresIn;
    private $accessToken;
    private $carbonNowMock;

    /**
     * @return void
     */
    public function setUp() : void
    {
        // Mock Carbon `now()`.
        Carbon::setTestNow(Carbon::create(2019, 9, 1, 12, 0, 0));

        // Set testing variables.
        $this->expiresIn = 3600;
        $this->expiresAt = 1567342800;
        $this->accessToken = 'access-token-string';

        parent::setUp();
    }

    /**
     * @return void
     */
    public function test_state_is_instantiable() : void
    {
        $state = new State($this->accessToken, $this->expiresIn);

        $this->assertInstanceOf(State::class, $state);
    }

    /**
     * @return void
     */
    public function test_state_increments_token_expiry() : void
    {
        $state = new State($this->accessToken, $this->expiresIn);

        $this->assertEquals($state->getExpiresAt(), $this->expiresAt);
        $this->assertEquals($state->getAccessToken(), $this->accessToken);
    }
}
