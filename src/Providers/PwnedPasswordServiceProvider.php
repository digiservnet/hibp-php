<?php
/**
 * PwnedPassword Service Provider
 *
 * @author Ian <ian@ianh.io>
 * @since 20/03/2019
 */

namespace Icawebdesign\Hibp\Providers;

use Icawebdesign\Hibp\Password\PwnedPassword;
use Illuminate\Support\ServiceProvider;

class PwnedPasswordServiceProvider extends ServiceProvider
{
    public function boot()
    {

    }

    public function register()
    {
        $this->app->bind('password', function () {
            return new PwnedPassword();
        });
    }
}