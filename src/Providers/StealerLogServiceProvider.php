<?php

namespace Icawebdesign\Hibp\Providers;

use Icawebdesign\Hibp\HibpHttp;
use Carbon\Laravel\ServiceProvider;

use Icawebdesign\Hibp\StealerLog\StealerLog;

use function getenv;

class StealerLogServiceProvider extends ServiceProvider
{
    protected HibpHttp $hibpHttp;

    public function boot(): void
    {
        $this->hibpHttp = new HibpHttp(getenv('HIBP_API_KEY') ?: null);
    }

    public function register(): void
    {
        $this->app->bind('stealerlog', fn (): StealerLog => new StealerLog($this->hibpHttp));
    }
}
