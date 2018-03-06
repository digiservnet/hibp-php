<?php
/**
 * Paste
 *
 * @author Ian <ian@ianh.io>
 * @since 05/03/2018
 */

namespace Icawebdesign\Hibp\Paste;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Tightenco\Collect\Support\Collection;

class Paste implements PasteInterface
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
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Check for any pastes containing specified email address
     *
     * @param string $emailAddress
     *
     * @return Collection
     * @throws GuzzleException
     */
    public function lookup(string $emailAddress): Collection
    {
        try {
            $response = $this->client->request('GET',
                sprintf('%s/pasteaccount/%s', $this->config['api_root'], $emailAddress)
            );
        } catch (GuzzleException $e) {
            $this->statusCode = $e->getCode();
            throw $e;
        }

        $this->statusCode = $response->getStatusCode();
        return collect(json_decode((string)$response->getBody()))
            ->map(function($paste) {
                return new PasteEntity($paste);
            });
    }
}
