<?php

namespace Icawebdesign\Hibp\Subscription;

use Icawebdesign\Hibp\HibpHttp;
use GuzzleHttp\ClientInterface;
use Icawebdesign\Hibp\Traits\HibpConfig;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Icawebdesign\Hibp\Exception\UnauthorizedException;
use Icawebdesign\Hibp\Exception\PasteNotFoundException;

use function sprintf;
use function urlencode;
use function json_decode;

class Subscription implements SubscriptionInterface
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

    public function status(array $options = []): SubscriptionStatusEntity
    {
        try {
            $response = $this->client->request(
                'GET',
                sprintf('%s/subscription/status', $this->apiRoot),
                $options,
            );
        } catch (ClientException $exception) {
            $this->statusCode = $exception->getCode();

            throw match ($exception->getCode()) {
                401 => new UnauthorizedException($exception->getMessage(), $exception->getCode()),
                400 => new RequestException($exception->getMessage(), $exception->getRequest()),
                default => $exception,
            };
        }

        $this->statusCode = $response->getStatusCode();

        $data = json_decode((string)$response->getBody(), associative: false);

        return new SubscriptionStatusEntity($data);
    }
}
