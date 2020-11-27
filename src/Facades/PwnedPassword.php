<?php
/**
 * PwnedPassword facade
 *
 * @author Ian <ian@ianh.io>
 * @since 20/03/2019
 */

namespace Icawebdesign\Hibp\Facades;

use Illuminate\Support\Facades\Facade;

class PwnedPassword extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'password';
    }
}
