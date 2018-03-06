<?php
/**
 * Breach interface
 *
 * @author Ian <ian@ianh.io>
 * @since 04/03/2018
 */

namespace Icawebdesign\Hibp\Breach;

interface BreachInterface
{
    /**
     * @param array $config
     */
    public function __construct(array $config);

    /**
     * @return int
     */
    public function getStatusCode(): int;
}
