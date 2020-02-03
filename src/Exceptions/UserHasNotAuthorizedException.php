<?php

namespace Spotify\Exceptions;

class UserHasNotAuthorizedException extends \Exception
{
    protected $code = 403;

    protected $message = 'User has not yet been authorized.';
}
