<?php
namespace Icawebdesign\Hibp\Providers;

use Icawebdesign\Hibp\HibpHttp;
use Icawebdesign\Hibp\Password\PwnedPassword;
use Illuminate\Support\ServiceProvider;

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
