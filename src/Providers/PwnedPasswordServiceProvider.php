<?php

namespace Icawebdesign\Hibp\Providers;

use Icawebdesign\Hibp\HibpHttp;
use Illuminate\Support\ServiceProvider;
use Icawebdesign\Hibp\Password\PwnedPassword;

/**
 * @codeCoverageIgnore
 */
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
