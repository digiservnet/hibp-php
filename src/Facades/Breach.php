<?php

namespace Icawebdesign\Hibp\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @codeCoverageIgnore
 */
class Breach extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'breach';
    }
}
