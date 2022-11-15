<?php
/**
 * Paste interface
 *
 * @author Ian <ian.h@digiserv.net>
 * @since 05/03/2018
 */

namespace Icawebdesign\Hibp\Paste;

use Icawebdesign\Hibp\HibpHttp;
use Illuminate\Support\Collection;

interface PasteInterface
{
    public function __construct(HibpHttp $hibpHttp);

    public function lookup(string $emailAddress, array $options = []): Collection;
}
