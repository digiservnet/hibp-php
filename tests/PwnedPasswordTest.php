<?php

namespace Icawebdesign\Hibp\Tests;

use Mockery;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Icawebdesign\Hibp\HibpHttp;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Collection;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Icawebdesign\Hibp\Password\PwnedPassword;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Icawebdesign\Hibp\Exception\PaddingHashCollisionException;

class PwnedPasswordTest extends TestCase
{
    protected PwnedPassword $pwnedPassword;

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    /** @test */
    public function successful_range_lookup_returns_a_positive_integer(): void
    {
        $list = self::mockPasswordListSha1();

        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andReturn(new Response(HttpResponse::HTTP_OK, [], $list));

        $pwnedPassword = new PwnedPassword(new HibpHttp(client: $client));
        $count = $pwnedPassword->rangeFromHash(self::generateSha1Hash('password'));

        self::assertSame(HttpResponse::HTTP_OK, $pwnedPassword->statusCode);
        self::assertIsInt($count);
        self::assertSame(3861493, $count);
    }

    /** @test */
    public function successful_ntlm_range_lookup_returns_a_positive_integer(): void
    {
        $list = self::mockPasswordListNtlm();

        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andReturn(new Response(HttpResponse::HTTP_OK, [], $list));

        $pwnedPassword = new PwnedPassword(new HibpHttp(client: $client));
        $count = $pwnedPassword->ntlmRangeFromHash(self::generateNtlmHash('password'));

        self::assertSame(HttpResponse::HTTP_OK, $pwnedPassword->statusCode);
        self::assertIsInt($count);
        self::assertSame(9659365, $count);
    }

    /** @test */
    public function invalid_range_request_throws_a_request_exception(): void
    {
        $this->expectException(RequestException::class);

        $mockedResponse = Mockery::mock(Response::class);
        $mockedResponse
            ->expects('getStatusCode')
            ->once()
            ->andReturn(HttpResponse::HTTP_BAD_REQUEST);

        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andThrow(new ClientException(
                message: 'The hash prefix was not in a valid format',
                request: Mockery::mock(Request::class),
                response: $mockedResponse,
            ));

        $pwnedPassword = new PwnedPassword(new HibpHttp(client: $client));
        $pwnedPassword->rangeFromHash(hash: '&&');
    }

    /** @test */
    public function invalid_ntlm_range_request_throws_a_request_exception(): void
    {
        $this->expectException(RequestException::class);

        $mockedResponse = Mockery::mock(Response::class);
        $mockedResponse
            ->expects('getStatusCode')
            ->once()
            ->andReturn(HttpResponse::HTTP_BAD_REQUEST);

        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andThrow(new ClientException(
                message: 'The hash prefix was not in a valid format',
                request: Mockery::mock(Request::class),
                response: $mockedResponse,
            ));

        $pwnedPassword = new PwnedPassword(new HibpHttp(client: $client));
        $pwnedPassword->ntlmRangeFromHash(hash: '&&');
    }

    /** @test */
    public function invalid_range_lookup_throws_a_client_exception(): void
    {
        $this->expectException(ClientException::class);

        $mockedResponse = Mockery::mock(Response::class);
        $mockedResponse
            ->expects('getStatusCode')
            ->once()
            ->andReturn(0);

        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andThrow(new ClientException(
                message: 'The hash prefix was not in a valid format',
                request: Mockery::mock(Request::class),
                response: $mockedResponse,
            ));

        $pwnedPassword = new PwnedPassword(new HibpHttp(client: $client));
        $pwnedPassword->rangeFromHash(hash: '&&');
    }

    /** @test */
    public function invalid_ntlm_range_lookup_throws_a_client_exception(): void
    {
        $this->expectException(ClientException::class);

        $mockedResponse = Mockery::mock(Response::class);
        $mockedResponse
            ->expects('getStatusCode')
            ->once()
            ->andReturn(0);

        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andThrow(new ClientException(
                message: 'The hash prefix was not in a valid format',
                request: Mockery::mock(Request::class),
                response: $mockedResponse,
            ));

        $pwnedPassword = new PwnedPassword(new HibpHttp(client: $client));
        $pwnedPassword->ntlmRangeFromHash(hash: '&&');
    }

    /** @test */
    public function successful_range_with_padding_returns_a_positive_integer(): void
    {
        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andReturn(new Response(HttpResponse::HTTP_OK, [], self::mockPasswordListSha1()));

        $pwnedPassword = new PwnedPassword(new HibpHttp(client: $client));
        $count = $pwnedPassword->paddedRangeFromHash(self::generateSha1Hash('password'));

        self::assertSame(HttpResponse::HTTP_OK, $pwnedPassword->statusCode);
        self::assertIsInt($count);
        self::assertSame(3861493, $count);
    }

    /** @test */
    public function successful_ntlm_range_with_padding_returns_a_positive_integer(): void
    {
        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andReturn(new Response(HttpResponse::HTTP_OK, [], self::mockPasswordListNtlm()));

        $pwnedPassword = new PwnedPassword(new HibpHttp(client: $client));
        $count = $pwnedPassword->paddedNtlmRangeFromHash(self::generateNtlmHash('password'));

        self::assertSame(HttpResponse::HTTP_OK, $pwnedPassword->statusCode);
        self::assertIsInt($count);
        self::assertSame(9659365, $count);
    }

    /** @test */
    public function failed_range_with_padding_returns_zero(): void
    {
        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andReturn(new Response(HttpResponse::HTTP_OK, [], self::mockPasswordListSha1()));

        $pwnedPassword = new PwnedPassword(new HibpHttp(client: $client));
        $count = $pwnedPassword->paddedRangeFromHash('0000000000000000000000000000000000000000');

        self::assertSame(HttpResponse::HTTP_OK, $pwnedPassword->statusCode);
        self::assertIsInt($count);
        self::assertSame(0, $count);
    }

    /** @test */
    public function failed_ntlm_range_with_padding_returns_zero(): void
    {
        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andReturn(new Response(HttpResponse::HTTP_OK, [], self::mockPasswordListSha1()));

        $pwnedPassword = new PwnedPassword(new HibpHttp(client: $client));
        $count = $pwnedPassword->paddedNtlmRangeFromHash('00000000000000000000000000000000');

        self::assertSame(HttpResponse::HTTP_OK, $pwnedPassword->statusCode);
        self::assertIsInt($count);
        self::assertSame(0, $count);
    }

    /** @test */
    public function successful_range_with_padding_and_padding_header_returns_a_positive_integer(): void
    {
        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andReturn(new Response(HttpResponse::HTTP_OK, [], self::mockPasswordListSha1()));

        $pwnedPassword = new PwnedPassword(new HibpHttp(client: $client));
        $count = $pwnedPassword->paddedRangeFromHash(
            hash: self::generateSha1Hash('password'),
            options: [
                'headers' => [
                    'Add-Padding' => true,
                ],
            ],
        );

        self::assertSame(HttpResponse::HTTP_OK, $pwnedPassword->statusCode);
        self::assertIsInt($count);
        self::assertSame(3861493, $count);
    }

    /** @test */
    public function successful_ntlm_range_with_padding_and_padding_header_returns_a_positive_integer(): void
    {
        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andReturn(new Response(HttpResponse::HTTP_OK, [], self::mockPasswordListNtlm()));

        $pwnedPassword = new PwnedPassword(new HibpHttp(client: $client));
        $count = $pwnedPassword->paddedNtlmRangeFromHash(
            hash: self::generateNtlmHash('password'),
            options: [
                'headers' => [
                    'Add-Padding' => true,
                ],
            ],
        );

        self::assertSame(HttpResponse::HTTP_OK, $pwnedPassword->statusCode);
        self::assertIsInt($count);
        self::assertSame(9659365, $count);
    }

    /** @test */
    public function invalid_range_request_with_padding_throws_request_exception(): void
    {
        $this->expectException(RequestException::class);

        $mockedResponse = Mockery::mock(Response::class);
        $mockedResponse
            ->expects('getStatusCode')
            ->once()
            ->andReturn(400);

        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andThrow(new ClientException(
                message: 'The hash prefix was not in a valid format',
                request: Mockery::mock(Request::class),
                response: $mockedResponse,
            ));

        $pwnedPassword = new PwnedPassword(new HibpHttp(client: $client));
        $pwnedPassword->paddedRangeFromHash(hash: '&&');
    }

    /** @test */
    public function invalid_ntlm_range_request_with_padding_throws_request_exception(): void
    {
        $this->expectException(RequestException::class);

        $mockedResponse = Mockery::mock(Response::class);
        $mockedResponse
            ->expects('getStatusCode')
            ->once()
            ->andReturn(400);

        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andThrow(new ClientException(
                message: 'The hash prefix was not in a valid format',
                request: Mockery::mock(Request::class),
                response: $mockedResponse,
            ));

        $pwnedPassword = new PwnedPassword(new HibpHttp(client: $client));
        $pwnedPassword->paddedNtlmRangeFromHash(hash: '&&');
    }

    /** @test */
    public function invalid_range_with_padding_throws_request_exception(): void
    {
        $this->expectException(RequestException::class);

        $mockedResponse = Mockery::mock(Response::class);
        $mockedResponse
            ->expects('getStatusCode')
            ->once()
            ->andReturn(0);

        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andThrow(new ClientException(
                message: 'message',
                request: Mockery::mock(Request::class),
                response: $mockedResponse,
            ));

        $pwnedPassword = new PwnedPassword(new HibpHttp(client: $client));
        $pwnedPassword->paddedRangeFromHash(hash: '&&');
    }

    /** @test */
    public function invalid_ntlm_range_with_padding_throws_request_exception(): void
    {
        $this->expectException(RequestException::class);

        $mockedResponse = Mockery::mock(Response::class);
        $mockedResponse
            ->expects('getStatusCode')
            ->once()
            ->andReturn(0);

        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andThrow(new ClientException(
                message: 'message',
                request: Mockery::mock(Request::class),
                response: $mockedResponse,
            ));

        $pwnedPassword = new PwnedPassword(new HibpHttp(client: $client));
        $pwnedPassword->paddedNtlmRangeFromHash(hash: '&&');
    }

    /** @test */
    public function failed_range_lookup_returns_zero(): void
    {
        $client = Mockery::mock(Client::class);
        $client->allows([
            'request' => new Response(HttpResponse::HTTP_OK, [], self::mockPasswordListSha1()),
        ]);

        $pwnedPassword = new PwnedPassword(new HibpHttp(client: $client));
        $count = $pwnedPassword->rangeFromHash('0000000000000000000000000000000000000000');

        self::assertSame(HttpResponse::HTTP_OK, $pwnedPassword->statusCode);
        self::assertEquals(0, $count);
    }

    /** @test */
    public function failed_ntlm_range_lookup_returns_zero(): void
    {
        $client = Mockery::mock(Client::class);
        $client->allows([
            'request' => new Response(HttpResponse::HTTP_OK, [], self::mockPasswordListSha1()),
        ]);

        $pwnedPassword = new PwnedPassword(new HibpHttp(client: $client));
        $count = $pwnedPassword->ntlmRangeFromHash('00000000000000000000000000000000');

        self::assertSame(HttpResponse::HTTP_OK, $pwnedPassword->statusCode);
        self::assertEquals(0, $count);
    }

    /** @test */
    public function successful_range_data_lookup_returns_a_valid_collection(): void
    {
        $client = Mockery::mock(Client::class);
        $client->allows([
            'request' => new Response(200, [], self::mockPasswordListSha1()),
        ]);

        $pwnedPassword = new PwnedPassword(new HibpHttp(client: $client));
        $response = $pwnedPassword->rangeDataFromHash('5BAA61E4C9B93F3F0682250B6CF8331B7EE68FD8');

        self::assertSame(200, $pwnedPassword->statusCode);
        self::assertGreaterThan(0, $response->last()['count']);
    }

    /** @test */
    public function invalid_range_data_request_throws_a_request_exception(): void
    {
        $this->expectException(RequestException::class);

        $mockedResponse = Mockery::mock(Response::class);
        $mockedResponse
            ->expects('getStatusCode')
            ->once()
            ->andReturn(HttpResponse::HTTP_BAD_REQUEST);

        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andThrow(new ClientException(
                message: 'message',
                request: Mockery::mock(Request::class),
                response: $mockedResponse,
            ));

        $pwnedPassword = new PwnedPassword(new HibpHttp(client: $client));
        $pwnedPassword->rangeDataFromHash('5BAA61E4C9B93F3F0682250B6CF8331B7EE68FD8');
    }

    /** @test */
    public function invalid_range_data_lookup_throws_a_request_exception(): void
    {
        $this->expectException(ClientException::class);

        $mockedResponse = Mockery::mock(Response::class);
        $mockedResponse
            ->expects('getStatusCode')
            ->once()
            ->andReturn(0);

        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andThrow(new ClientException(
                message: 'message',
                request: Mockery::mock(Request::class),
                response: $mockedResponse,
            ));

        $pwnedPassword = new PwnedPassword(new HibpHttp(client: $client));
        $pwnedPassword->rangeDataFromHash('5BAA61E4C9B93F3F0682250B6CF8331B7EE68FD8');
    }

    /** @test */
    public function successful_padded_range_data_lookup_returns_a_valid_collection_with_zero_count_elements(): void
    {
        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andReturn(new Response(HttpResponse::HTTP_OK, [], self::mockPasswordListSha1()));

        $pwnedPassword = new PwnedPassword(new HibpHttp(client: $client));
        $response = $pwnedPassword->paddedRangeDataFromHash(self::generateSha1Hash('password'));

        self::assertSame(HttpResponse::HTTP_OK, $pwnedPassword->statusCode);
        self::assertSame(5, $response->last()['count']);
    }

    /** @test */
    public function invalid_padded_range_data_request_throws_a_request_exception(): void
    {
        $this->expectException(RequestException::class);

        $mockedResponse = Mockery::mock(Response::class);
        $mockedResponse
            ->expects('getStatusCode')
            ->once()
            ->andReturn(HttpResponse::HTTP_BAD_REQUEST);

        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andThrow(new ClientException(
                message: '',
                request: Mockery::mock(Request::class),
                response: $mockedResponse,
            ));

        $pwnedPassword = new PwnedPassword(new HibpHttp(client: $client));
        $pwnedPassword->paddedRangeDataFromHash('5BAA61E4C9B93F3F0682250B6CF8331B7EE68FD8');
    }

    /** @test */
    public function invalid_padded_range_data_lookup_throws_a_request_exception(): void
    {
        $this->expectException(ClientException::class);

        $mockedResponse = Mockery::mock(Response::class);
        $mockedResponse
            ->expects('getStatusCode')
            ->once()
            ->andReturn(0);

        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andThrow(new ClientException(
                message: '',
                request: Mockery::mock(Request::class),
                response: $mockedResponse,
            ));

        $pwnedPassword = new PwnedPassword(new HibpHttp(client: $client));
        $pwnedPassword->paddedRangeDataFromHash('5BAA61E4C9B93F3F0682250B6CF8331B7EE68FD8');
    }

    /** @test */
    public function stripped_successful_padded_range_returns_a_valid_collection_without_zero_count_elements(): void
    {
        $hash = self::generateSha1Hash('password');

        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andReturn(new Response(HttpResponse::HTTP_OK, [], self::mockPasswordListSha1()));

        $pwnedPassword = new PwnedPassword(new HibpHttp(client: $client));
        $response = $pwnedPassword->paddedRangeDataFromHash($hash);

        self::assertSame(HttpResponse::HTTP_OK, $pwnedPassword->statusCode);
        self::assertGreaterThan(
            0,
            PwnedPassword::stripZeroMatchesData($response, $hash)->last()
        );
    }

    /** @test */
    public function stripping_zero_matches_from_response_with_provided_hash_throws_padding_hash_collision_exception(): void
    {
        $this->expectException(PaddingHashCollisionException::class);

        $hash = self::generateSha1Hash('password');

        $hashes = Collection::make([
            $hash => [
                'count' => 0,
                'hashSnippet' => $hash,
                'matched' => true,
            ],
        ]);

        PwnedPassword::stripZeroMatchesData($hashes, $hash);
    }

    private static function mockPasswordListSha1(): string
    {
        $data = file_get_contents(sprintf('%s/_responses/passwords/password_results_sha1.txt', __DIR__));

        return (false !== $data) ? trim($data) : '';
    }

    private static function mockPasswordListNtlm(): string
    {
        $data = file_get_contents(sprintf('%s/_responses/passwords/password_results_ntlm.txt', __DIR__));

        return (false !== $data) ? trim($data) : '';
    }

    private static function generateSha1Hash(string $value): string
    {
        return strtoupper(sha1($value));
    }

    private static function generateNtlmHash(string $value): string
    {
        $unicodeValue = iconv('UTF-8', 'UTF-16LE', $value);
        $key = hash('md4', (string)$unicodeValue);

        return strtoupper($key);
    }
}
