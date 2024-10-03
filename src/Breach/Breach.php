<?php

declare(strict_types=1);

namespace Icawebdesign\Hibp\Breach;

use stdClass;
use Exception;
use JsonException;
use GuzzleHttp\ClientInterface;
use Icawebdesign\Hibp\HibpHttp;
use Illuminate\Support\Collection;
use Icawebdesign\Hibp\Traits\HibpConfig;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Icawebdesign\Hibp\Exception\BreachNotFoundException;

use Icawebdesign\Hibp\Exception\InvalidBreachSiteDataException;

use function trim;

use const JSON_THROW_ON_ERROR;

class Breach implements BreachInterface
{
    use HibpConfig;

    public int $statusCode;
    protected ClientInterface $client;
    protected string $apiRoot;

    public function __construct(HibpHttp $hibpHttp)
    {
        $this->apiRoot = "{$this->hibp['api_root']}/v{$this->hibp['api_version']}";
        $this->client = $hibpHttp->client();
    }

    /**
     * Get all breach sites in system
     *
     * @param ?string $domainFilter
     * @param array $options
     *
     * @return Collection
     * @throws GuzzleException|JsonException
     */
    public function getAllBreachSites(?string $domainFilter = null, array $options = []): Collection
    {m
        $uri = "{$this->apiRoot}/breaches";
        $uri = $this->filterDomain($uri, $domainFilter);

        try {
            $response = $this->client->request(
                'GET',
                $uri,
                $options,
            );
        } catch (RequestException $exception) {
            $this->statusCode = $exception->getCode();
            throw $exception;
        }

        $this->statusCode = $response->getStatusCode();

        try {
            $data = json_decode((string)$response->getBody(), associative: false, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new InvalidBreachSiteDataException();
        }

        return Collection::make($data)
            ->map(static fn (stdClass $breach): BreachSiteEntity => new BreachSiteEntity($breach));
    }

    private function filterDomain(string $uri, ?string $domainFilter): string
    {
        if ($this->hasDomainFilter($domainFilter)) {
            $uri = sprintf('%s?domain=%s', $uri, urlencode($domainFilter));
        }

        return $uri;
    }

    private function hasDomainFilter(?string $domainFilter): bool
    {
        return (null !== $domainFilter) && ('' !== trim($domainFilter));
    }

    /**
     * Get breach data for single account
     *
     * @param string $account
     * @param array $options
     *
     * @return BreachSiteEntity
     * @throws Exception|GuzzleException|JsonException
     */
    public function getBreach(string $account, array $options = []): BreachSiteEntity
    {
        try {
            $response = $this->client->request(
                'GET',
                sprintf('%s/breach/%s', $this->apiRoot, urlencode($account)),
                $options,
            );
        } catch (ClientException $exception) {
            $this->statusCode = $exception->getCode();

            throw match ($exception->getCode()) {
                404 => new BreachNotFoundException($exception->getMessage()),
                400 => new RequestException($exception->getMessage(), $exception->getRequest()),
                default => $exception,
            };
        }

        $data = json_decode((string)$response->getBody(), associative: false, flags: JSON_THROW_ON_ERROR);

        return new BreachSiteEntity($data);
    }

    /**
     * Get list of all data classes in the system
     *
     * @param array $options
     *
     * @return Collection
     * @throws GuzzleException|JsonException
     */
    public function getAllDataClasses(array $options = []): Collection
    {
        try {
            $response = $this->client->request(
                'GET',
                "{$this->apiRoot}/dataclasses",
                $options,
            );
        } catch (ClientException $exception) {
            $this->statusCode = $exception->getCode();
            throw $exception;
        }

        $this->statusCode = $response->getStatusCode();

        $data = json_decode((string)$response->getBody(), associative: true, flags: JSON_THROW_ON_ERROR);

        return Collection::make($data);
    }

    /**
     * Get list of breached sites an email address was found in
     *
     * @param string $emailAddress
     * @param bool $includeUnverified
     * @param ?string $domainFilter
     * @param array $options
     *
     * @return Collection
     * @throws GuzzleException|JsonException|Exception
     */
    public function getBreachedAccount(
        string $emailAddress,
        bool $includeUnverified = false,
        ?string $domainFilter = null,
        array $options = [],
    ): Collection {
        $uri = sprintf(
            '%s/breachedaccount/%s?truncateResponse=false&includeUnverified=%s',
            $this->apiRoot,
            urlencode($emailAddress),
            $includeUnverified,
        );

        $uri = $this->filterDomain($uri, $domainFilter);

        try {
            $response = $this->client->request(
                'GET',
                $uri,
                $options,
            );
        } catch (ClientException $exception) {
            $this->statusCode = $exception->getCode();

            throw match ($exception->getCode()) {
                404 => new BreachNotFoundException($exception->getMessage()),
                400 => new RequestException($exception->getMessage(), $exception->getRequest()),
                default => $exception,
            };
        }

        $this->statusCode = $response->getStatusCode();

        $data = json_decode((string)$response->getBody(), associative: false, flags: JSON_THROW_ON_ERROR);

        return Collection::make($data)
            ->map(static fn (stdClass $breach): BreachSiteEntity => new BreachSiteEntity($breach));
    }

    /**
     * Get breach data for an account but only return breach name
     *
     * @param string $emailAddress
     * @param bool $includeUnverified
     * @param ?string $domainFilter
     * @param array $options
     *
     * @return Collection
     * @throws GuzzleException|JsonException
     */
    public function getBreachedAccountTruncated(
        string $emailAddress,
        bool $includeUnverified = false,
        ?string $domainFilter = null,
        array $options = [],
    ): Collection {
        $uri = sprintf(
            '%s/breachedaccount/%s?truncateResponse=true&includeUnverified=%s',
            $this->apiRoot,
            urlencode($emailAddress),
            $includeUnverified ? 'true' : 'false',
        );

        $uri = $this->filterDomain($uri, $domainFilter);

        try {
            $response = $this->client->request(
                'GET',
                $uri,
                $options,
            );
        } catch (ClientException $exception) {
            $this->statusCode = $exception->getCode();

            throw match ($exception->getCode()) {
                404 => new BreachNotFoundException($exception->getMessage()),
                400 => new RequestException($exception->getMessage(), $exception->getRequest()),
                default => $exception,
            };
        }

        $this->statusCode = $response->getStatusCode();

        $data = json_decode((string)$response->getBody(), associative: false, flags: JSON_THROW_ON_ERROR);

        return Collection::make($data)
            ->map(static fn (stdClass $breach): BreachSiteTruncatedEntity => new BreachSiteTruncatedEntity($breach));
    }

    /**
     * @param array $options
     *
     * @return BreachSiteEntity
     * @throws GuzzleException
     * @throws Exception|GuzzleException|JsonException
     */
    public function getLatestBreach(array $options = []): BreachSiteEntity
    {
        try {
            $response = $this->client->request(
                'GET',
                "{$this->apiRoot}/latestbreach",
                $options,
            );
        } catch (ClientException $exception) {
            $this->statusCode = $exception->getCode();

            throw match ($exception->getCode()) {
                404 => new BreachNotFoundException($exception->getMessage()),
                400 => new RequestException($exception->getMessage(), $exception->getRequest()),
                default => $exception,
            };
        }

        $data = json_decode((string)$response->getBody(), associative: false, flags: JSON_THROW_ON_ERROR);

        return new BreachSiteEntity($data);
    }
}
