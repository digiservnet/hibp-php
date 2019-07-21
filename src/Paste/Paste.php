<?php
/**
 * Paste
 *
 * @author Ian <ian@ianh.io>
 * @since 05/03/2018
 */

namespace Icawebdesign\Hibp\Paste;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Icawebdesign\Hibp\Hibp;

class Paste implements PasteInterface
{
    /** @var Client */
    protected $client;

    /** @var int */
    protected $statusCode;

    /** @var string */
    protected $apiRoot;

    public function __construct(string $apiKey)
    {
        $config = (new Hibp())->loadConfig();
        $this->apiRoot = $config['hibp']['api_root'] . '/v' . $config['hibp']['api_version'];
        $this->client = new Client([
            'headers' => [
                'User-Agent' => $config['global']['user_agent'],
                'hibp-api-key' => $apiKey,
            ],
        ]);
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
            $response = $this->client->request(
                'GET',
                $this->apiRoot . '/pasteaccount/' . urlencode($emailAddress)
            );
        } catch (ClientException $e) {
            $this->statusCode = $e->getCode();

            switch ($e->getCode()) {
                case 400:
                    throw new RequestException($e->getMessage(), $e->getRequest());
                    break;

                case 404:
                    throw new \Icawebdesign\Hibp\Exception\PasteNotFoundException($e->getMessage());
                    break;

                default:
                    throw $e;
                    break;
            }
        }

        $this->statusCode = $response->getStatusCode();
        $collection = new \Tightenco\Collect\Support\Collection();

        return $collection->make(json_decode((string)$response->getBody()))
            ->map(function ($paste) {
                return new PasteEntity($paste);
            });
    }
}
