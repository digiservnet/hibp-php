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
    /** @var string */
    protected string $apiKey = '';

    public function boot(): void
    {
        $this->apiKey = env('HIBP_API_KEY');
    }

    public function register(): void
    {
        $this->app->bind('paste', function () {
            return new Paste($this->apiKey);
        });
    }
}
