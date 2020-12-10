<?php

namespace Icawebdesign\Hibp\Password;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Icawebdesign\Hibp\Exception\PaddingHashCollisionException;
use Icawebdesign\Hibp\Hibp;
use Icawebdesign\Hibp\Model\PwnedPassword as PasswordData;
use Illuminate\Support\Collection;

/**
 * PwnedPassword module
 *
 * @author Ian <ian@ianh.io>
 * @since 27/02/2018
 */
class PwnedPassword implements PwnedPasswordInterface
{
    /** @var Client */
    protected Client $client;

    /** @var int */
    protected int $statusCode;

    /** @var string */
    protected string $apiRoot;

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
     * @param string $hash
     * @param array $options
     *
     * @return int
     * @throws GuzzleException
     */
    public function rangeFromHash(string $hash, array $options = []): int
    {
        $hash = strtoupper($hash);
        $hashSnippet = substr($hash, 0, 5);

        try {
            $response = $this->client->request(
                'GET',
                sprintf('%s/range/%s', $this->apiRoot, $hashSnippet),
                $options
            );
        } catch (RequestException $e) {
            $this->statusCode = $e->getCode();
            throw $e;
        }

        $this->statusCode = $response->getStatusCode();

        $match = (new PasswordData())->getRangeData($response, $hash);

        if ($match->collapse()->has($hash)) {
            return $match->collapse()->get($hash)['count'];
        }

        return 0;
    }

    /**
     * @param string $hash
     * @param array $options
     *
     * @return int
     * @throws GuzzleException
     */
    public function paddedRangeFromHash(string $hash, array $options = []): int
    {
        $hash = strtoupper($hash);
        $hashSnippet = substr($hash, 0, 5);

        if (array_key_exists('headers', $options)) {
            $headers = $options['headers'];
            $headers['Add-Padding'] = 'true';
            $options['headers'] = $headers;
        } else {
            $options['headers'] = ['Add-Padding' => 'true'];
        }

        try {
            $response = $this->client->request(
                'GET',
                sprintf('%s/range/%s', $this->apiRoot, $hashSnippet),
                $options
            );
        } catch (RequestException $e) {
            $this->statusCode = $e->getCode();
            throw $e;
        }

        $this->statusCode = $response->getStatusCode();

        $match = (new PasswordData())->getRangeDataWithPadding($response, $hash);

        if ($match->collapse()->has($hash)) {
            return $match->collapse()->get($hash)['count'];
        }

        return 0;
    }

    /**
     * @param string $hash
     * @param array $options
     *
     * @return Collection
     * @throws GuzzleException
     */
    public function rangeDataFromHash(string $hash, array $options = []): Collection
    {
        $hash = strtoupper($hash);
        $hashSnippet =substr($hash, 0, 5);

        try {
            $response = $this->client->request(
                'GET',
                sprintf('%s/range/%s', $this->apiRoot, $hashSnippet),
                $options
            );
        } catch (RequestException $e) {
            $this->statusCode = $e->getCode();
            throw $e;
        }

        $this->statusCode = $response->getStatusCode();

        return (new PasswordData())->getRangeData($response, $hash)->collapse();
    }

    /**
     * @param string $hash
     * @param array $options
     *
     * @return Collection
     * @throws GuzzleException
     */
    public function paddedRangeDataFromHash(string $hash, array $options = []): Collection
    {
        $hash = strtoupper($hash);
        $hashSnippet =substr($hash, 0, 5);
        $options['Add-Padding'] = 'true';

        $request = new Request(
            'GET',
            sprintf('%s/range/%s', $this->apiRoot, $hashSnippet),
            $options
        );

        try {
            $response = $this->client->send($request);
        } catch (RequestException $e) {
            $this->statusCode = $e->getCode();
            throw $e;
        }

        $this->statusCode = $response->getStatusCode();

        return (new PasswordData())->getRangeDataWithPadding($response, $hash)->collapse();
    }

    /**
     * @param Collection $data
     * @param string $hash
     *
     * @return Collection
     */
    public static function stripZeroMatchesData(Collection $data, string $hash): Collection
    {
        $hash = strtoupper($hash);

        return $data->filter(static function ($value) use ($hash) {
            if (($value['hashSnippet'] === $hash) && (0 === $value['count'])) {
                throw new PaddingHashCollisionException('Padding hash collision');
            }

            return $value['count'] > 0;
        });
    }
}
