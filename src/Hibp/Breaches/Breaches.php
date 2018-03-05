<?php

namespace Icawebdesign\Hibp\Breaches;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;
use Tightenco\Collect\Support\Collection;

/**
 * Breaches module
 *
 * @author Ian <ian@ianh.io>
 * @since 04/03/2018
 */
class Breaches implements BreachesInterface
{
    /** @var array */
    protected $config;

    /** @var Client */
    protected $client;

    /** @var int */
    protected $statusCode;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
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
                sprintf('%s/breaches', $this->config['api_root'])
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
     * @return stdClass
     * @throws GuzzleException
     */
    public function getBreach(string $account): BreachSiteEntity
    {
        try {
            $response = $this->client->request('GET',
                sprintf('%s/breach/%s', $this->config['api_root'], $account)
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
                sprintf('%s/dataclasses', $this->config['api_root'])
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
                sprintf('%s/breachedaccount/%s',
                    $this->config['api_root'],
                    urlencode($emailAddress)
                )
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
