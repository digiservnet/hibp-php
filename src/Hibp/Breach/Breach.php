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
     * Get all breach sites in system
     *
     * @return \Tightenco\Collect\Support\Collection
     * @throws GuzzleException
     */
    public function getAllBreachSites(): \Tightenco\Collect\Support\Collection
    {
        try {
            $response = $this->client->request(
                'GET',
                $this->apiRoot . '/breaches'
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
     *
     * @return \Tightenco\Collect\Support\Collection
     * @throws GuzzleException
     */
    public function getBreachedAccount(string $emailAddress): \Tightenco\Collect\Support\Collection
    {
        try {
            $response = $this->client->request(
                'GET',
                $this->apiRoot . '/breachedaccount/' . urlencode($emailAddress)
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
}
