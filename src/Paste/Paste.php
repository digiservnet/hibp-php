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
use Icawebdesign\Hibp\Exception\PasteNotFoundException;
use Icawebdesign\Hibp\Hibp;
use Tightenco\Collect\Support\Collection;

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
        $this->apiRoot = sprintf('%s/v%d', $config['hibp']['api_root'], $config['hibp']['api_version']);
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
     * @return Collection
     * @throws GuzzleException
     */
    public function lookup(string $emailAddress): Collection
    {
        try {
            $response = $this->client->request(
                'GET',
                sprintf('%s/pasteaccount/%s', $this->apiRoot, urlencode($emailAddress))
            );
        } catch (ClientException $e) {
            $this->statusCode = $e->getCode();

            switch ($e->getCode()) {
                case 400:
                    throw new RequestException($e->getMessage(), $e->getRequest());
                    break;

                case 404:
                    throw new PasteNotFoundException($e->getMessage());
                    break;

                default:
                    throw $e;
                    break;
            }
        }

        $this->statusCode = $response->getStatusCode();

        return (new Collection())->make(json_decode((string)$response->getBody()))
            ->map(function ($paste) {
                return new PasteEntity($paste);
            });
    }
}
