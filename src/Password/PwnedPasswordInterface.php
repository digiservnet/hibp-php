<?php
/**
 * PwnedPassword interface
 *
 * @author Ian <ian@ianh.io>
 * @since 27/02/2018
 */

namespace Icawebdesign\Hibp\Password;

use Icawebdesign\Hibp\HibpHttp;
use Illuminate\Support\Collection;

interface PwnedPasswordInterface
{
    public function __construct(HibpHttp $hibpHttp);

    public function getStatusCode(): int;

    public function rangeFromHash(string $hash): int;

    public function rangeDataFromHash(string $hash): Collection;

    public function paddedRangeFromHash(string $hash): int;

    public function paddedRangeDataFromHash(string $hash): Collection;
}
