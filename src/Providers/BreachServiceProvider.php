<?php
/**
 * Breach Service Provider
 *
 * @author Ian <ian@ianh.io>
 * @since 20/03/2019
 */

namespace Icawebdesign\Hibp\Providers;

use Icawebdesign\Hibp\Breach\Breach;
use Illuminate\Support\ServiceProvider;

class BreachServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //
    }

    public function register()
    {
        $this->app->bind('breach', function () {
            return new Breach();
        });
    }
}
