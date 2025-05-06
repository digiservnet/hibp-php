<?php

namespace Icawebdesign\Hibp\StealerLog;

use Icawebdesign\Hibp\HibpHttp;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\Collection;
use Icawebdesign\Hibp\Traits\HibpConfig;
use GuzzleHttp\Exception\RequestException;

class StealerLog implements StealerLogInterface
{
    use HibpConfig;

    protected ClientInterface $client;

    public int $statusCode;

    protected string $apiRoot;

    public function __construct(HibpHttp $hibpHttp)
    {
        $this->apiRoot = "{$this->hibp['api_root']}/v{$this->hibp['api_version']}";
        $this->client = $hibpHttp->client();
    }

    public function getStealerLogsByEmailAddress(string $emailAddress, array $options = []): Collection
    {
        $uri = "{$this->apiRoot}/stealerlogsbyemail/{$emailAddress}";

        $response = $this->client->request(
            'GET',
            $uri,
            $options,
        );

        $this->statusCode = $response->getStatusCode();

        $data = json_decode((string)$response->getBody(), associative: false, flags: JSON_THROW_ON_ERROR);

        return Collection::make($data);
    }

    public function getStealerLogsByWebsiteDomain(string $domain, array $options = []): Collection
    {
        $uri = "{$this->apiRoot}/stealerlogsbywebsitedomain/{$domain}";

        $response = $this->client->request(
            'GET',
            $uri,
            $options,
        );

        $this->statusCode = $response->getStatusCode();

        $data = json_decode((string)$response->getBody(), associative: false, flags: JSON_THROW_ON_ERROR);

        return Collection::make($data);
    }

    public function getStealerLogsByEmailDomain(string $domain, array $options = []): Collection
    {
        $uri = "{$this->apiRoot}/stealerlogsbyemaildomain/{$domain}";

        $response = $this->client->request(
            'GET',
            $uri,
            $options,
        );

        $this->statusCode = $response->getStatusCode();

        $data = json_decode((string)$response->getBody(), associative: true, flags: JSON_THROW_ON_ERROR);

        return Collection::make($data);
    }
}
