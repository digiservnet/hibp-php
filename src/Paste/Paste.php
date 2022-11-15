<?php

namespace Icawebdesign\Hibp\Paste;

use stdClass;
use GuzzleHttp\ClientInterface;
use Icawebdesign\Hibp\HibpHttp;
use Illuminate\Support\Collection;
use Icawebdesign\Hibp\Traits\HibpConfig;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Icawebdesign\Hibp\Exception\PasteNotFoundException;

class Paste implements PasteInterface
{
    use HibpConfig;

    protected ClientInterface $client;

    public int $statusCode;

    protected string $apiRoot;

    public function __construct(HibpHttp $hibpHttp)
    {
        $this->apiRoot = "{$this->hibp['api_root']}/v/{$this->hibp['api_version']}";
        $this->client = $hibpHttp->client();
    }

    /**
     * Check for any pastes containing specified email address
     *
     * @param string $emailAddress
     * @param array $options
     *
     * @return Collection
     * @throws GuzzleException
     */
    public function lookup(string $emailAddress, array $options = []): Collection
    {
        try {
            $response = $this->client->request(
                'GET',
                sprintf('%s/pasteaccount/%s', $this->apiRoot, urlencode($emailAddress)),
                $options
            );
        } catch (ClientException $exception) {
            $this->statusCode = $exception->getCode();

            throw match ($exception->getCode()) {
                404 => new PasteNotFoundException($exception->getMessage()),
                400 => new RequestException($exception->getMessage(), $exception->getRequest()),
                default => $exception,
            };
        }

        $this->statusCode = $response->getStatusCode();

        return Collection::make(json_decode((string)$response->getBody(), associative: false))
            ->map(static fn (stdClass $paste): PasteEntity => new PasteEntity($paste));
    }
}
