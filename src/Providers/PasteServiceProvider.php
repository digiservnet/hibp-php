<?php
/**
 * Paste Service Provider
 *
 * @author Ian <ian.h@digiserv.net>
 * @since 20/03/2019
 */

namespace Icawebdesign\Hibp\Providers;

use Icawebdesign\Hibp\HibpHttp;
use Icawebdesign\Hibp\Paste\Paste;
use Illuminate\Support\ServiceProvider;

class PasteServiceProvider extends ServiceProvider
{
    protected HibpHttp $hibpHttp;

    public function boot(): void
    {
        $this->hibpHttp = new HibpHttp(env('HIBP_API_KEY'));
    }

    public function register(): void
    {
        $this->app->bind('paste', function () {
            return new Paste($this->hibpHttp);
        });
    }
}
