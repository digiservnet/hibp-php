<?php

namespace Icawebdesign\Hibp\Password;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class PwnedPassword implements PwnedPasswordInterface
{
    /** @var array */
    protected $config;

    /** @var Client */
    protected $client;

    /** @var int */
    protected $statusCode;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->client = new Client();
    }

    /**
     * Return the last response status code
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Retrieve number of times a password has been listed
     *
     * @param string $password
     *
     * @throws GuzzleException
     * @return int
     */
    public function lookup(string $password): int
    {
        try {
            $response = $this->client->request('GET',
                sprintf('%s/pwnedpassword/%s', $this->config['api_root'], $password)
            );
        } catch (GuzzleException $e) {
            $this->statusCode = $e->getCode();
            throw $e;
        }

        $this->statusCode = $response->getStatusCode();

        return (int)$response->getBody();
    }

    /**
     * @param string $hashSnippet
     * @param string $hash
     *
     * @throws GuzzleException
     * @return int
     */
    public function range(string $hashSnippet, string $hash)
    {
        $hashSnippet = strtoupper($hashSnippet);
        $hash = strtoupper($hash);

        try {
            $response = $this->client->request('GET',
                sprintf('%s/range/%s', $this->config['api_root'], $hashSnippet)
            );
        } catch (GuzzleException $e) {
            $this->statusCode = $e->getCode();
            throw $e;
        }

        $this->statusCode = $response->getStatusCode();

        $results = collect(explode("\r\n", (string)$response->getBody()));
        $match = $results->map(function($hashSuffix) use ($hashSnippet, $hash) {
            list($suffix, $count) = explode(':', $hashSuffix);
            $fullHash = sprintf('%s%s', $hashSnippet, $suffix);

            return collect([
                $fullHash => [
                    'hashSnippet' => $fullHash,
                    'count' => (int)$count,
                    'matched' => $fullHash == $hash,
                ],
            ]);
        });

        if ($match->collapse()->has($hash)) {
            return $match->collapse()->get($hash)['count'];
        }

        return 0;
    }
}
