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
    public function boot(): void
    {
        //
    }

    public function register(): void
    {
        $this->app->bind('password', function () {
            return new PwnedPassword();
        });
    }
}
