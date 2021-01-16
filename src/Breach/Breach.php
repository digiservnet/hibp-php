<?php

namespace Icawebdesign\Hibp\Breach;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Icawebdesign\Hibp\Exception\BreachNotFoundException;
use Icawebdesign\Hibp\Hibp;
use Icawebdesign\Hibp\HibpHttp;
use Illuminate\Support\Collection;

/**
 * Breach module
 *
 * @author Ian <ian@ianh.io>
 * @since 04/03/2018
 */
class Breach implements BreachInterface
{
    /** @var Client */
    protected Client $client;

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
     * Get all breach sites in system
     *
     * @param ?string $domainFilter
     *
     * @return Collection
     * @throws GuzzleException
     */
    public function getAllBreachSites(string $domainFilter = null): Collection
    {
        $uri = sprintf('%s/breaches', $this->apiRoot);

        if ((null !== $domainFilter) && ('' !== trim($domainFilter))) {
            $uri = sprintf('%s?domain=%s', $uri, urlencode($domainFilter));
        }

        try {
            $response = $this->client->request(
                'GET',
                $uri
            );
        } catch (RequestException $e) {
            $this->statusCode = $e->getCode();
            throw $e;
        }

        $this->statusCode = $response->getStatusCode();

        return Collection::make(json_decode((string)$response->getBody(), false))
            ->map(static function ($breach) {
                return new BreachSiteEntity($breach);
            });
    }

    /**
     * Get breach data for single account
     *
     * @param string $account
     *
     * @return BreachSiteEntity
     * @throws Exception|GuzzleException
     */
    public function getBreach(string $account): BreachSiteEntity
    {
        try {
            $response = $this->client->request(
                'GET',
                sprintf('%s/breach/%s', $this->apiRoot, urlencode($account))
            );
        } catch (ClientException $e) {
            $this->statusCode = $e->getCode();

            switch ($e->getCode()) {
                case 404:
                    throw new BreachNotFoundException($e->getMessage());

                case 400:
                    throw new RequestException($e->getMessage(), $e->getRequest());

                default:
                    throw $e;
            }
        }

        return new BreachSiteEntity(json_decode((string)$response->getBody(), false));
    }

    /**
     * Get list of all data classes in the system
     *
     * @return Collection
     * @throws GuzzleException
     */
    public function getAllDataClasses(): Collection
    {
        try {
            $response = $this->client->request(
                'GET',
                sprintf('%s/dataclasses', $this->apiRoot)
            );
        } catch (ClientException $e) {
            $this->statusCode = $e->getCode();
            throw $e;
        }

        $this->statusCode = $response->getStatusCode();

        return Collection::make(json_decode((string)$response->getBody(), false));
    }

    /**
     * Get list of breached sites an email address was found in
     *
     * @param string $emailAddress
     * @param bool $includeUnverified
     * @param ?string $domainFilter
     *
     * @return Collection
     * @throws GuzzleException
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function getBreachedAccount(
        string $emailAddress,
        bool $includeUnverified = false,
        string $domainFilter = null
    ): Collection {
        $uri = sprintf(
            '%s/breachedaccount/%s?truncateResponse=false&includeUnverified=%s',
            $this->apiRoot,
            urlencode($emailAddress),
            $includeUnverified ? 'true' : 'false'
        );

        if ((null !== $domainFilter) && ('' !== trim($domainFilter))) {
            $uri = sprintf('%s&domain=%s', $uri, urlencode($domainFilter));
        }

        try {
            $response = $this->client->request(
                'GET',
                $uri
            );
        } catch (ClientException $e) {
            $this->statusCode = $e->getCode();

            switch ($e->getCode()) {
                case 404:
                    throw new BreachNotFoundException($e->getMessage());

                case 400:
                    throw new RequestException($e->getMessage(), $e->getRequest());

                default:
                    throw $e;
            }
        }

        $this->statusCode = $response->getStatusCode();

        return Collection::make(json_decode((string)$response->getBody(), false))
            ->map(static function ($breach) {
                return new BreachSiteEntity($breach);
            });
    }

    /**
     * Get breach data for an account but only return breach name
     *
     * @param string $emailAddress
     * @param bool $includeUnverified
     * @param ?string $domainFilter
     *
     * @return Collection
     * @throws GuzzleException
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function getBreachedAccountTruncated(
        string $emailAddress,
        bool $includeUnverified = false,
        string $domainFilter = null
    ): Collection {
        $uri = sprintf(
            '%s/breachedaccount/%s?truncateResponse=true&includeUnverified=%s',
            $this->apiRoot,
            urlencode($emailAddress),
            $includeUnverified ? 'true' : 'false'
        );

        if ((null !== $domainFilter) && ('' !== trim($domainFilter))) {
            $uri = sprintf('%s&domain=%s', $uri, urlencode($domainFilter));
        }

        try {
            $response = $this->client->request(
                'GET',
                $uri
            );
        } catch (RequestException $e) {
            $this->statusCode = $e->getCode();
            throw $e;
        }

        $this->statusCode = $response->getStatusCode();

        return Collection::make(json_decode((string)$response->getBody(), false))
            ->map(static function ($breach) {
                return new BreachSiteTruncatedEntity($breach);
            });
    }
}
