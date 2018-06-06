<?php

namespace Icawebdesign\Hibp\Breach;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Tightenco\Collect\Support\Collection;

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

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
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
     * @return Collection
     * @throws GuzzleException
     */
    public function getAllBreachSites(): Collection
    {
        try {
            $response = $this->client->request('GET',
                $this->apiRoot . '/breaches'
            );
        } catch (GuzzleException $e) {
            $this->statusCode = $e->getCode();
            throw $e;
        }

        $this->statusCode = $response->getStatusCode();

        return collect(json_decode((string)$response->getBody()))
            ->map(function($breach) {
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
            $response = $this->client->request('GET',
                $this->apiRoot . '/breach/' . urlencode($account)
            );
        } catch (GuzzleException $e) {
            $this->statusCode = $e->getCode();
            throw $e;
        }

        return new BreachSiteEntity(json_decode((string)$response->getBody()));
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
            $response = $this->client->request('GET',
                $this->apiRoot . '/dataclasses'
            );
        } catch (GuzzleException $e) {
            $this->statusCode = $e->getCode();
            throw $e;
        }

        $this->statusCode = $response->getStatusCode();

        return collect(json_decode((string)$response->getBody()));
    }

    /**
     * Get list of breached sites an email address was found in
     *
     * @param string $emailAddress
     *
     * @return Collection
     * @throws GuzzleException
     */
    public function getBreachedAccount(string $emailAddress): Collection
    {
        try {
            $response = $this->client->request('GET',
                $this->apiRoot . '/breachedaccount/' . urlencode($emailAddress)
            );
        } catch (GuzzleException $e) {
            $this->statusCode = $e->getCode();
            throw $e;
        }

        $this->statusCode = $response->getStatusCode();

        return collect(json_decode((string)$response->getBody()))
            ->map(function($breach) {
                return new BreachSiteEntity($breach);
            });
    }
}
