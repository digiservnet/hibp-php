<?php

namespace Icawebdesign\Hibp\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @codeCoverageIgnore
 */
class Paste extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'paste';
    }
}
