<?php

namespace Icawebdesign\Hibp\Providers;

use Icawebdesign\Hibp\HibpHttp;
use Icawebdesign\Hibp\Paste\Paste;
use Illuminate\Support\ServiceProvider;

/**
 * @codeCoverageIgnore
 */
class PasteServiceProvider extends ServiceProvider
{
    protected HibpHttp $hibpHttp;

    public function boot(): void
    {
        $this->hibpHttp = new HibpHttp(getenv('HIBP_API_KEY'));
    }

    public function register(): void
    {
        $this->app->bind('paste', function () {
            return new Paste($this->hibpHttp);
        });
    }
}
