<?php

namespace Icawebdesign\Hibp\Breach;

use Icawebdesign\Hibp\HibpHttp;
use Illuminate\Support\Collection;

interface BreachInterface
{
    public function __construct(HibpHttp $client);

    public function getAllBreachSites(string $domainFilter = null, array $options = []): Collection;

    public function getBreach(string $account, array $options = []): BreachSiteEntity;

    public function getAllDataClasses(array $options = []): Collection;

    public function getBreachedAccount(
        string $emailAddress,
        bool $includeUnverified = false,
        string $domainFilter = null,
        array $options = []
    ): Collection;

    public function getBreachedAccountTruncated(
        string $emailAddress,
        bool $includeUnverified = false,
        string $domainFilter = null,
        array $options = []
    ): Collection;
}
