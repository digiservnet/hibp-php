<?php
/**
 * Paste interface
 *
 * @author Ian <ian@ianh.io>
 * @since 05/03/2018
 */

namespace Icawebdesign\Hibp\Paste;

use Tightenco\Collect\Support\Collection;

interface PasteInterface
{
    /**
     * @param array $config
     */
    public function __construct();

    /**
     * @return int
     */
    public function getStatusCode(): int;

    /**
     * @param string $emailAddress
     *
     * @return Collection
     */
    public function lookup(string $emailAddress): Collection;
}
