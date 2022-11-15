<?php

namespace Icawebdesign\Hibp\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @codeCoverageIgnore
 */
class PwnedPassword extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'password';
    }
}
