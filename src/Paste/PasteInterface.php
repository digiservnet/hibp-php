<?php
/**
 * Paste interface
 *
 * @author Ian <ian@ianh.io>
 * @since 05/03/2018
 */

namespace Icawebdesign\Hibp\Paste;

use Icawebdesign\Hibp\HibpHttp;
use Illuminate\Support\Collection;

interface PasteInterface
{
    /**
     * PasteInterface constructor.
     *
     * @param HibpHttp $hibpHttp
     */
    public function __construct(HibpHttp $hibpHttp);

    /**
     * @return int
     */
    public function getStatusCode(): int;

    /**
     * @param string $emailAddress
     * @param array $options
     *
     * @return Collection
     */
    public function lookup(string $emailAddress, array $options = []): Collection;
}
