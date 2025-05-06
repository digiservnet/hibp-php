<?php

namespace Icawebdesign\Hibp\StealerLog;

use Icawebdesign\Hibp\HibpHttp;
use Illuminate\Support\Collection;

interface StealerLogInterface
{
    public function __construct(HibpHttp $client);

    public function getStealerLogsByEmailAddress(string $emailAddress, array $options = []): Collection;

    public function getStealerLogsByWebsiteDomain(string $domain, array $options = []): Collection;

    public function getStealerLogsByEmailDomain(string $domain, array $options = []): Collection;
}
