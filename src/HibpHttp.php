<?php

namespace Icawebdesign\Hibp;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class HibpHttp
{
    protected ClientInterface $client;

    public function __construct(string $apiKey = null, ClientInterface $client = null)
    {
        if (null !== $client) {
            $this->client = $client;

            return;
        }

        $config = (new Hibp())->loadConfig();
        $headers = [
            'User-Agent' => $config['global']['user_agent'],
        ];

        if (null !== $apiKey) {
            $headers['hibp-api-key'] = $apiKey;
        }

        $this->client = new Client([
            'headers' => $headers
        ]);
    }

    public function client(): ClientInterface
    {
        return $this->client;
    }
}
