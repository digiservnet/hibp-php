<?php

namespace Icawebdesign\Hibp\Password;

use Icawebdesign\Hibp\HibpHttp;
use Illuminate\Support\Collection;

interface PwnedPasswordInterface
{
    public function __construct(HibpHttp $hibpHttp);

    public function rangeFromHash(string $hash, array $options): int;

    public function rangeDataFromHash(string $hash, array $options): Collection;

    public function paddedRangeFromHash(string $hash, array $options): int;

    public function paddedRangeDataFromHash(string $hash, array $options): Collection;
}
