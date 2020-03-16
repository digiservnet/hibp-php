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
    /** @var string */
    protected $apiKey = '';

    public function boot(): void
    {
        $this->apiKey = env('HIBP_API_KEY');
    }

    public function register(): void
    {
        $this->app->bind('breach', function () {
            return new Breach($this->apiKey);
        });
    }
}
