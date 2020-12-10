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

    public function rangeFromHash(string $hash, array $options): int;

    public function rangeDataFromHash(string $hash, array $options): Collection;

    public function paddedRangeFromHash(string $hash, array $options): int;

    public function paddedRangeDataFromHash(string $hash, array $options): Collection;
}
