<?php
/**
 * Breach Service Provider
 *
 * @author Ian <ian@ianh.io>
 * @since 20/03/2019
 */

namespace Icawebdesign\Hibp\Providers;

use Icawebdesign\Hibp\Breach\Breach;
use Icawebdesign\Hibp\HibpHttp;
use Illuminate\Support\ServiceProvider;

class BreachServiceProvider extends ServiceProvider
{
    protected HibpHttp $hibpHttp;

    public function boot(): void
    {
        $this->hibpHttp = new HibpHttp(env('HIBP_API_KEY'));
    }

    public function register(): void
    {
        $this->app->bind('breach', function () {
            return new Breach($this->hibpHttp);
        });
    }
}
