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
     * PasteInterface constructor.
     *
     * @param string $apiKey
     */
    public function __construct(string $apiKey);

    /**
     * @return int
     */
    public function getStatusCode(): int;

    /**
     * @param string $emailAddress
     * @param array $options
     *
     * @return Collection
     */
    public function lookup(string $emailAddress, array $options = []): Collection;
}
