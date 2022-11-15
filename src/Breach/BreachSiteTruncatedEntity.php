<?php

namespace Icawebdesign\Hibp\Breach;

use stdClass;

class BreachSiteTruncatedEntity
{
    public readonly string $name;

    public function __construct(stdClass $data)
    {
        if (isset($data->Name)) {
            $this->name = $data->Name;
        }
    }
}
