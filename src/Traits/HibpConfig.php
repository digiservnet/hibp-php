<?php

namespace Icawebdesign\Hibp\Traits;

trait HibpConfig
{
    protected string $userAgent = 'hibp-php/6';

    protected array $hibp = [
        'api_root' => 'https://haveibeenpwned.com/api',
        'api_version' => 3,
    ];

    protected array $pwnedPasswords = [
        'api_root' => 'https://api.pwnedpasswords.com',
    ];
}
