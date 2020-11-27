<?php
/**
 * Breach facade
 *
 * @author Ian <ian@ianh.io>
 * @since 20/03/2019
 */

namespace Icawebdesign\Hibp\Facades;

use Illuminate\Support\Facades\Facade;

class Breach extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'breach';
    }
}
