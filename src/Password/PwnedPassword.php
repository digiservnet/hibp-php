<?php

namespace Icawebdesign\Hibp\Password;

use Exception;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Icawebdesign\Hibp\Exception\PaddingHashCollisionException;
use Icawebdesign\Hibp\HibpHttp;
use Icawebdesign\Hibp\Model\PwnedPassword as PasswordData;
use Icawebdesign\Hibp\Traits\HibpConfig;
use Illuminate\Support\Collection;

class PwnedPassword implements PwnedPasswordInterface
{
    use HibpConfig;

    protected ClientInterface $client;
    public int $statusCode;
    protected string $apiRoot;

    public function __construct(HibpHttp $hibpHttp)
    {
        $this->apiRoot = $this->pwnedPasswords['api_root'];
        $this->client = $hibpHttp->client();
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
        $hashSnippet = substr($hash, offset: 0, length: 5);

        try {
            $response = $this->client->request(
                'GET',
                "{$this->apiRoot}/range/{$hashSnippet}",
                $options,
            );
        } catch (ClientException $exception) {
            $this->statusCode = $exception->getCode();

            throw match ($exception->getCode()) {
                400 => new RequestException($exception->getMessage(), $exception->getRequest()),
                default => $exception,
            };
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
    public function ntlmRangeFromHash(string $hash, array $options = []): int
    {
        $hash = strtoupper($hash);
        $hashSnippet = substr($hash, offset: 0, length: 5);

        try {
            $response = $this->client->request(
                'GET',
                "{$this->apiRoot}/range/{$hashSnippet}?mode=ntlm",
                $options,
            );
        } catch (ClientException $exception) {
            $this->statusCode = $exception->getCode();

            throw match ($exception->getCode()) {
                400 => new RequestException($exception->getMessage(), $exception->getRequest()),
                default => $exception,
            };
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
                sprintf("{$this->apiRoot}/range/{$hashSnippet}"),
                $options,
            );
        } catch (ClientException $exception) {
            $this->statusCode = $exception->getCode();

            throw match ($exception->getCode()) {
                400 => new RequestException($exception->getMessage(), $exception->getRequest()),
                default => $exception,
            };
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
     * @return int
     * @throws GuzzleException
     */
    public function paddedNtlmRangeFromHash(string $hash, array $options = []): int
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
                sprintf("{$this->apiRoot}/range/{$hashSnippet}?mode=ntlm"),
                $options,
            );
        } catch (ClientException $exception) {
            $this->statusCode = $exception->getCode();

            throw match ($exception->getCode()) {
                400 => new RequestException($exception->getMessage(), $exception->getRequest()),
                default => $exception,
            };
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
        $hashSnippet = substr($hash, 0, 5);

        try {
            $response = $this->client->request(
                'GET',
                sprintf('%s/range/%s', $this->apiRoot, $hashSnippet),
                $options,
            );
        } catch (ClientException $exception) {
            $this->statusCode = $exception->getCode();

            throw match ($exception->getCode()) {
                400 => new RequestException($exception->getMessage(), $exception->getRequest()),
                default => $exception,
            };
        }

        $this->statusCode = $response->getStatusCode();

        return (new PasswordData())->getRangeData($response, $hash)->collapse();
    }

    /**
     * @param string $hash
     * @param array $options
     *
     * @return Collection
     * @throws GuzzleException|Exception
     */
    public function paddedRangeDataFromHash(string $hash, array $options = []): Collection
    {
        $hash = strtoupper($hash);
        $hashSnippet = substr($hash, 0, 5);

        $uri = "{$this->apiRoot}/range/{$hashSnippet}";

        try {
            $response = $this->client->request(
                'GET',
                $uri,
                $options,
            );
        } catch (ClientException $exception) {
            $this->statusCode = $exception->getCode();

            throw match ($exception->getCode()) {
                400 => new RequestException($exception->getMessage(), $exception->getRequest()),
                default => $exception,
            };
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
