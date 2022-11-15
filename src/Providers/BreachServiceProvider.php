<?php

namespace Icawebdesign\Hibp\Providers;

use Icawebdesign\Hibp\HibpHttp;
use Icawebdesign\Hibp\Breach\Breach;
use Illuminate\Support\ServiceProvider;

/**
 * @codeCoverageIgnore
 */
class BreachServiceProvider extends ServiceProvider
{
    protected HibpHttp $hibpHttp;

    public function boot(): void
    {
        $this->hibpHttp = new HibpHttp(getenv('HIBP_API_KEY'));
    }

    public function register(): void
    {
        $this->app->bind('breach', function () {
            return new Breach($this->hibpHttp);
        });
    }
}
