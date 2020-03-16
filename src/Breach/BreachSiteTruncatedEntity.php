<?php
/**
 * Truncated data entity for breached accounts
 *
 * @author Ian <ian@ianh.io>
 * @since 22/03/2019
 */

namespace Icawebdesign\Hibp\Breach;

use stdClass;

class BreachSiteTruncatedEntity
{
    /** @var string */
    protected $name;

    /**
     * BreachSiteTruncatedEntity constructor.
     *
     * @param stdClass $data
     */
    public function __construct(stdClass $data)
    {
        if (isset($data->Name)) {
            $this->name = $data->Name;
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
