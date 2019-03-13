<?php
/**
 * PwnedPassword interface
 *
 * @author Ian <ian@ianh.io>
 * @since 27/02/2018
 */

namespace Icawebdesign\Hibp\Password;

use Tightenco\Collect\Support\Collection;

interface PwnedPasswordInterface
{
    public function __construct();

    /**
     * @return int
     */
    public function getStatusCode(): int;

    public function range(string $hashSnippet, string $hash): int;

    public function rangeData(string $hashSnippet, string $hash): Collection;
}
