<?php

namespace Icawebdesign\Hibp\Breach;

use stdClass;
use RuntimeException;

class BreachSiteTruncatedEntity
{
    public readonly string $name;
    public readonly string $title;

    public function __construct(?stdClass $data = null)
    {
        if (null === $data) {
            throw new RuntimeException('Invalid BreachSite data');
        }

        $this->name = $data->Name;
        $this->title = $data->Title;
    }
}
