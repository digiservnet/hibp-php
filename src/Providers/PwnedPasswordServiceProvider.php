<?php
/**
 * PwnedPassword Service Provider
 *
 * @author Ian <ian.h@digiserv.net>
 * @since 20/03/2019
 */

namespace Icawebdesign\Hibp\Providers;

use Icawebdesign\Hibp\HibpHttp;
use Icawebdesign\Hibp\Password\PwnedPassword;
use Illuminate\Support\ServiceProvider;

class PwnedPasswordServiceProvider extends ServiceProvider
{
    protected HibpHttp $hibpHttp;

    public function boot(): void
    {
        $this->hibpHttp = new HibpHttp();
    }

    public function register(): void
    {
        $this->app->bind('password', function () {
            return new PwnedPassword($this->hibpHttp);
        });
    }
}
