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
use Icawebdesign\Hibp\Hibp;

class Paste implements PasteInterface
{
    /** @var Client */
    protected $client;

    /** @var int */
    protected $statusCode;

    /** @var string */
    protected $apiRoot;

    public function __construct()
    {
        $config = Hibp::loadConfig();
        $this->apiRoot = $config['hibp']['api_root'] . '/v' . $config['hibp']['api_version'];
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
     * @return \Tightenco\Collect\Support\Collection
     * @throws GuzzleException
     */
    public function lookup(string $emailAddress): \Tightenco\Collect\Support\Collection
    {
        try {
            $response = $this->client->request('GET',
                $this->apiRoot . '/pasteaccount/' . urlencode($emailAddress)
            );
        } catch (GuzzleException $e) {
            $this->statusCode = $e->getCode();
            throw $e;
        }

        $this->statusCode = $response->getStatusCode();
        return \Tightenco\Collect\Support\Collection::make(json_decode((string)$response->getBody()))
            ->map(function($paste) {
                return new PasteEntity($paste);
            });
    }
}
