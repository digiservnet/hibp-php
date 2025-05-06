<?php

namespace Icawebdesign\Hibp\StealerLog;

use Icawebdesign\Hibp\HibpHttp;
use Illuminate\Support\Collection;

interface StealerLogInterface
{
    public function __construct(HibpHttp $client);

    public function getStealerLogsByEmail(string $emailAddress): Collection;
}
