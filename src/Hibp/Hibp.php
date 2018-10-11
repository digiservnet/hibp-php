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
    private $config;

    /**
     * Load config file
     *
     * @return array
     * @throws ParseException
     */
    public function loadConfig(): array
    {
        $yaml = new Yaml();
        $this->config = $yaml->parseFile(__DIR__ . '/../config/config.yml');

        return $this->config;
    }
}
