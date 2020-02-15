<?php

namespace Spotify\Exceptions;

class UserRequiresAuthorizationException extends \Exception
{
    protected $code = 403;

    protected $message = 'User needs to authorize.';
}
