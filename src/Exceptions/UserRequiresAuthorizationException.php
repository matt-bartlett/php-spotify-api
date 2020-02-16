<?php

namespace Spotify\Exceptions;

class UserRequiresAuthorizationException extends \Exception
{
    /**
     * @var int
     */
    protected $code = 403;

    /**
     * @var string
     */
    protected $message = 'User needs to authorize.';
}
