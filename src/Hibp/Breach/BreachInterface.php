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
     * @return Collection
     */
    public function getAllBreachSites(): Collection;

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
     *
     * @return Collection
     */
    public function getBreachedAccount(string $emailAddress): Collection;
}
