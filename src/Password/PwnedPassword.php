<?php

namespace Icawebdesign\Hibp\Password;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Icawebdesign\Hibp\Hibp;
use Icawebdesign\Hibp\Model\PwnedPassword as PasswordData;
use Tightenco\Collect\Support\Collection;

/**
 * PwnedPassword module
 *
 * @author Ian <ian@ianh.io>
 * @since 27/02/2018
 */
class PwnedPassword implements PwnedPasswordInterface
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

        $this->apiRoot = $config['pwned_passwords']['api_root'];
        $this->client = new Client([
            'headers' => [
                'User-Agent' => $config['global']['user_agent'],
            ],
        ]);
    }

    /**
     * Return the last response status code
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param string $hashSnippet
     * @param string $hash
     *
     * @throws GuzzleException
     * @return int
     *
     * @deprecated 4.0.0 Use rangeFromHash() method instead
     */
    public function range(string $hashSnippet, string $hash): int
    {
        $hashSnippet = strtoupper($hashSnippet);
        $hash = strtoupper($hash);

        try {
            $response = $this->client->request(
                'GET',
                $this->apiRoot . '/range/' . $hashSnippet
            );
        } catch (RequestException $e) {
            $this->statusCode = $e->getCode();
            throw $e;
        }

        $this->statusCode = $response->getStatusCode();

        $pwnedPassword = new PasswordData();
        $match = $pwnedPassword->getRangeData($response, $hash);

        if ($match->collapse()->has($hash)) {
            return $match->collapse()->get($hash)['count'];
        }

        return 0;
    }

    /**
     * @param string $hashSnippet
     * @param string $hash
     *
     * @return Collection
     * @throws GuzzleException
     *
     * @deprecated 4.0.0 Use rangeDataFromHash() method instead
     */
    public function rangeData(string $hashSnippet, string $hash): Collection
    {
        $hashSnippet = strtoupper($hashSnippet);
        $hash = strtoupper($hash);

        try {
            $response = $this->client->request(
                'GET',
                $this->apiRoot . '/range/' . $hashSnippet
            );
        } catch (RequestException $e) {
            $this->statusCode = $e->getCode();
            throw $e;
        }

        $this->statusCode = $response->getStatusCode();

        $pwnedPassword = new PasswordData();
        $match = $pwnedPassword->getRangeData($response, $hash);

        return $match->collapse();
    }

    /**
     * @param string $hash
     *
     * @return int
     * @throws GuzzleException
     */
    public function rangeFromHash(string $hash): int
    {
        $hash = strtoupper($hash);
        $hashSnippet = substr($hash, 0, 5);

        try {
            $response = $this->client->request(
                'GET',
                sprintf('%s/range/%s', $this->apiRoot, $hashSnippet)
            );
        } catch (RequestException $e) {
            $this->statusCode = $e->getCode();
            throw $e;
        }

        $this->statusCode = $response->getStatusCode();

        $pwnedPassword = new PasswordData();
        $match = $pwnedPassword->getRangeData($response, $hash);

        if ($match->collapse()->has($hash)) {
            return $match->collapse()->get($hash)['count'];
        }

        return 0;
    }

    /**
     * @param string $hash
     *
     * @return Collection
     * @throws GuzzleException
     */
    public function rangeDataFromHash(string $hash): Collection
    {
        $hash = strtoupper($hash);
        $hashSnippet =substr($hash, 0, 5);

        try {
            $response = $this->client->request(
                'GET',
                sprintf('%s/range/%s', $this->apiRoot, $hashSnippet)
            );
        } catch (RequestException $e) {
            $this->statusCode = $e->getCode();
            throw $e;
        }

        $this->statusCode = $response->getStatusCode();

        $pwnedPassword = new PasswordData();
        $match = $pwnedPassword->getRangeData($response, $hash);

        return $match->collapse();
    }
}
