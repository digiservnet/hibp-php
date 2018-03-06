<?php
/**
 * Pastes interface
 *
 * @author Ian <ian@ianh.io>
 * @since 05/03/2018
 */

namespace Icawebdesign\Hibp\Pastes;

interface PastesInterface
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
