<?php
/**
 * Paste Service Provider
 *
 * @author Ian <ian@ianh.io>
 * @since 20/03/2019
 */

namespace Icawebdesign\Hibp\Providers;

use Icawebdesign\Hibp\Paste\Paste;
use Illuminate\Support\ServiceProvider;

class PasteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //
    }

    public function register()
    {
        $this->app->bind('paste', function () {
            return new Paste();
        });
    }
}
