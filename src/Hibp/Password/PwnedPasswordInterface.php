<?php
/**
 * DESCRIPTION_HERE
 *
 * @author Ian <ian@ianh.io>
 * @since 27/02/2018
 */

namespace Icawebdesign\Hibp\Password;

interface PwnedPasswordInterface
{
    /**
     * @param array $config
     */
    public function __construct(array $config);

    public function getStatusCode(): int;
}