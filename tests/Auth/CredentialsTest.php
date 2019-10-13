<?php

namespace Spotify\Tests\Auth;

use Spotify\Auth\Credentials;
use PHPUnit\Framework\TestCase;

class CredentialsTest extends TestCase
{
    /**
     * @return void
     */
    public function test_credentials_is_instantiable() : void
    {
        $clientId = 'client-id';
        $clientSecret = 'client-secret';
        $redirectUrl = 'https://redirect.me';

        $credentials = new Credentials($clientId, $clientSecret, $redirectUrl);

        $this->assertInstanceOf(Credentials::class, $credentials);
        $this->assertEquals($credentials->getClientId(), $clientId);
        $this->assertEquals($credentials->getClientSecret(), $clientSecret);
        $this->assertEquals($credentials->getRedirectUrl(), $redirectUrl);
    }
}
