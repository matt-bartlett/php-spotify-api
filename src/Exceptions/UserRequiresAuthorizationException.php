<?php

namespace Spotify\Exceptions;

class UserRequiresAuthorizationException extends \Exception
{
    /**
     * @var int
     */
    protected $code = 401;

    /**
     * @var string
     */
    protected $message = 'User needs to authorize.';
}
