<?php

namespace Icawebdesign\Hibp;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Icawebdesign\Hibp\Traits\HibpConfig;

class HibpHttp
{
    use HibpConfig;

    protected ClientInterface $client;

    public function __construct(string $apiKey = null, ClientInterface $client = null)
    {
        $headers = [
            'User-Agent' => $this->userAgent,
        ];

        if (null !== $apiKey) {
            $headers['hibp-api-key'] = $apiKey;
        }

        if (null !== $client) {
            $this->client = $client;

            return;
        }

        $this->client = new Client([
            'headers' => $headers,
        ]);
    }

    public function client(): ClientInterface
    {
        return $this->client;
    }
}
