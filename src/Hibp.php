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
    protected array $config;

    /**
     * Load config file
     *
     * @return array
     * @throws ParseException
     */
    public function loadConfig(): array
    {
        $this->config = Yaml::parseFile(sprintf('%s/config/config.yml', __DIR__));

        return $this->config;
    }
}
