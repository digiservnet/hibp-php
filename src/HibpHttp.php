<?php

namespace Icawebdesign\Hibp;

use GuzzleHttp\Client;

class HibpHttp
{
    protected Client $client;

    public function __construct(string $apiKey = null, Client $client = null)
    {
        if (null !== $client) {
            $this->client = $client;

            return;
        }

        $config = (new Hibp())->loadConfig();
        $this->client = new Client([
            'headers' => [
                'User-Agent' => $config['global']['user_agent'],
                'hibp-api-key' => $apiKey,
            ],
        ]);
    }

    public function client(): Client
    {
        return $this->client;
    }
}
