<?php
/**
 * Breach interface
 *
 * @author Ian <ian@ianh.io>
 * @since 04/03/2018
 */

namespace Icawebdesign\Hibp\Breach;

use Icawebdesign\Hibp\HibpHttp;
use Illuminate\Support\Collection;

interface BreachInterface
{
    /**
     * BreachInterface constructor.
     *
     * @param HibpHttp $client
     */
    public function __construct(HibpHttp $client);

    /**
     * @return int
     */
    public function getStatusCode(): int;

    /**
     * @param ?string $domainFilter
     * @return Collection
     */
    public function getAllBreachSites(string $domainFilter = null): Collection;

    /**
     * @param string $account
     *
     * @return BreachSiteEntity
     */
    public function getBreach(string $account): BreachSiteEntity;

    /**
     * @return Collection
     */
    public function getAllDataClasses(): Collection;

    /**
     * @param string $emailAddress
     * @param bool $includeUnverified
     * @param ?string $domainFilter
     *
     * @return Collection
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function getBreachedAccount(
        string $emailAddress,
        bool $includeUnverified = false,
        string $domainFilter = null
    ): Collection;

    /**
     * @param string $emailAddress
     * @param bool $includeUnverified
     * @param ?string $domainFilter
     *
     * @return Collection
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function getBreachedAccountTruncated(
        string $emailAddress,
        bool $includeUnverified = false,
        string $domainFilter = null
    ): Collection;
}
