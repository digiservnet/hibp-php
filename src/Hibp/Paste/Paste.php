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
    /** @var Client */
    protected $client;

    /** @var int */
    protected $statusCode;

    /** @var string */
    protected $apiRoot;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->apiRoot = sprintf('%s/v%d',
            $config['api_root'],
            $config['api_version']
        );
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
                sprintf('%s/pasteaccount/%s', $this->apiRoot, $emailAddress)
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
