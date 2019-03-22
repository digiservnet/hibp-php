<?php

namespace Icawebdesign\Hibp\Breach;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Icawebdesign\Hibp\Exception\BreachNotFoundException;
use Icawebdesign\Hibp\Hibp;

/**
 * Breach module
 *
 * @author Ian <ian@ianh.io>
 * @since 04/03/2018
 */
class Breach implements BreachInterface
{
    /** @var Client */
    protected $client;

    /** @var int */
    protected $statusCode;

    /** @var string */
    protected $apiRoot;

    public function __construct()
    {
        $config = (new Hibp())->loadConfig();
        $this->apiRoot = $config['hibp']['api_root'] . '/v' . $config['hibp']['api_version'];
        $this->client = new Client([
            'headers' => [
                'User-Agent' => $config['global']['user_agent'],
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
     * Get all breach sites in system
     *
     * @param string $domainFilter
     *
     * @return \Tightenco\Collect\Support\Collection
     * @throws GuzzleException
     */
    public function getAllBreachSites(string $domainFilter = null): \Tightenco\Collect\Support\Collection
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
        $collection = new \Tightenco\Collect\Support\Collection();

        return $collection->make(json_decode((string)$response->getBody()))
            ->map(function ($breach) {
                return new BreachSiteEntity($breach);
            });
    }

    /**
     * Get breach data for single account
     *
     * @param string $account
     *
     * @return BreachSiteEntity
     * @throws GuzzleException
     */
    public function getBreach(string $account): BreachSiteEntity
    {
        try {
            $response = $this->client->request(
                'GET',
                $this->apiRoot . '/breach/' . urlencode($account)
            );
        } catch (ClientException $e) {
            $this->statusCode = $e->getCode();

            switch ($e->getCode()) {
                case 404:
                    throw new BreachNotFoundException($e->getMessage());
                    break;

                case 400:
                    throw new RequestException($e->getMessage(), $e->getRequest());
                    break;

                default:
                    throw $e;
                    break;
            }
        }

        return new BreachSiteEntity(json_decode((string)$response->getBody()));
    }

    /**
     * Get list of all data classes in the system
     *
     * @return \Tightenco\Collect\Support\Collection
     * @throws GuzzleException
     */
    public function getAllDataClasses(): \Tightenco\Collect\Support\Collection
    {
        try {
            $response = $this->client->request(
                'GET',
                $this->apiRoot . '/dataclasses'
            );
        } catch (GuzzleException $e) {
            $this->statusCode = $e->getCode();
            throw $e;
        }

        $this->statusCode = $response->getStatusCode();
        $collection = new \Tightenco\Collect\Support\Collection();

        return $collection->make(json_decode((string)$response->getBody()));
    }

    /**
     * Get list of breached sites an email address was found in
     *
     * @param string $emailAddress
     * @param bool $includeUnverified
     * @param string $domainFilter
     *
     * @return \Tightenco\Collect\Support\Collection
     * @throws GuzzleException
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function getBreachedAccount(
        string $emailAddress,
        bool $includeUnverified = false,
        string $domainFilter = null
    ): \Tightenco\Collect\Support\Collection {
        $uri = sprintf(
            '%s/breachedaccount/%s?includeUnverified=%s',
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
                    break;

                case 400:
                    throw new RequestException($e->getMessage(), $e->getRequest());
                    break;

                default:
                    throw $e;
                    break;
            }
        }

        $this->statusCode = $response->getStatusCode();
        $collection = new \Tightenco\Collect\Support\Collection();

        return $collection->make(json_decode((string)$response->getBody()))
            ->map(function ($breach) {
                return new BreachSiteEntity($breach);
            });
    }

    /**
     * Get breach data for an account but only return breach name
     *
     * @param string $emailAddress
     * @param bool $includeUnverified
     * @param string $domainFilter
     *
     * @return \Tightenco\Collect\Support\Collection
     * @throws GuzzleException
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function getBreachedAccountTruncated(
        string $emailAddress,
        bool $includeUnverified = false,
        string $domainFilter = null
    ): \Tightenco\Collect\Support\Collection {
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
        $collection = new \Tightenco\Collect\Support\Collection();

        return $collection->make(json_decode((string)$response->getBody()))
            ->map(function ($breach) {
                return new BreachSiteTruncatedEntity($breach);
            });
    }
}
