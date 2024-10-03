<?php

namespace Icawebdesign\Hibp\Exception;

use Throwable;
use RuntimeException;

class InvalidBreachSiteDataException extends RuntimeException
{
    public function __construct(string $message = 'Invalid BreachSite data', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(
            $message,
            $code,
            $previous,
        );
    }
}
