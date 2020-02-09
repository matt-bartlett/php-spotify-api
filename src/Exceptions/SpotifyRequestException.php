<?php

namespace Spotify\Exceptions;

class SpotifyRequestException extends \Exception
{
    /**
     * @param string $url
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $url, int $code, \Throwable $previous = null)
    {
        parent::__construct(sprintf('API call to [%s] has failed.', $url), $code, $previous);
    }
}
