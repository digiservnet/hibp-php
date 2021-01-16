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
        $this->client = new Client([
            'headers' => [
                'User-Agent' => $config['global']['user_agent'],
                'hibp-api-key' => $apiKey,
            ],
        ]);
    }

    public function client(): ClientInterface
    {
        return $this->client;
    }
}
