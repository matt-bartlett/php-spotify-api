<?php

namespace Spotify\Exceptions;

class AuthenticationException extends \Exception
{
    /**
     * @var int
     */
    protected $code = 401;

    /**
     * @var string
     */
    protected $message = 'Your access token is invalid or has expired.';
}
