<?php
/**
 * Paste interface
 *
 * @author Ian <ian@ianh.io>
 * @since 05/03/2018
 */

namespace Icawebdesign\Hibp\Paste;

interface PasteInterface
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
