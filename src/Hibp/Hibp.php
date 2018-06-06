<?php
/**
 * Global utility class
 *
 * @author Ian <ian@ianh.io>
 * @since 08/03/2018
 */

namespace Icawebdesign\Hibp;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class Hibp
{
    /** @var array */
    private static $config;

    /**
     * Load config file
     *
     * @return array
     * @throws ParseException
     */
    public static function loadConfig(): array
    {
        self::$config = Yaml::parseFile(__DIR__ . '/../config/config.yml');

        return self::$config;
    }
}
