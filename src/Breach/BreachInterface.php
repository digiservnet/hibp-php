<?php
/**
 * Breach interface
 *
 * @author Ian <ian@ianh.io>
 * @since 04/03/2018
 */

namespace Icawebdesign\Hibp\Breach;

use Tightenco\Collect\Support\Collection;

interface BreachInterface
{
    public function __construct();

    /**
     * @return int
     */
    public function getStatusCode(): int;

    /**
     * @param string $domainFilter
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
     * @param string $domainFilter
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
     * @param string $emailAccount
     * @param bool $includeUnverified
     * @param string $domainFilter
     *
     * @return Collection
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function getBreachedAccountTruncated(
        string $emailAccount,
        bool $includeUnverified = false,
        string $domainFilter = null
    ): Collection;
}
