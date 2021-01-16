<?php
/**
 * Paste
 *
 * @author Ian <ian@ianh.io>
 * @since 05/03/2018
 */

namespace Icawebdesign\Hibp\Paste;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Icawebdesign\Hibp\Exception\PasteNotFoundException;
use Icawebdesign\Hibp\Hibp;
use Icawebdesign\Hibp\HibpHttp;
use Illuminate\Support\Collection;

class Paste implements PasteInterface
{
    /** @var ClientInterface */
    protected ClientInterface $client;

    /** @var int */
    protected int $statusCode;

    /** @var string */
    protected string $apiRoot;

    public function __construct(HibpHttp $hibpHttp)
    {
        $config = (new Hibp())->loadConfig();
        $this->apiRoot = sprintf('%s/v%d', $config['hibp']['api_root'], $config['hibp']['api_version']);
        $this->client = $hibpHttp->client();
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

                case 404:
                    throw new PasteNotFoundException($e->getMessage());

                default:
                    throw $e;
            }
        }

        $this->statusCode = $response->getStatusCode();

        return Collection::make(json_decode((string)$response->getBody(), false))
            ->map(static function ($paste) {
                return new PasteEntity($paste);
            });
    }
}
