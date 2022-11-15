<?php

namespace Icawebdesign\Hibp\Tests;

use Mockery;
use RuntimeException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Icawebdesign\Hibp\HibpHttp;
use PHPUnit\Framework\TestCase;
use Icawebdesign\Hibp\Breach\Breach;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Icawebdesign\Hibp\Breach\BreachSiteEntity;
use Icawebdesign\Hibp\Breach\BreachSiteTruncatedEntity;
use Icawebdesign\Hibp\Exception\BreachNotFoundException;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class BreachTest extends TestCase
{
    protected string $apiKey = '';

    protected Breach $breach;

    protected const TOO_MANY_REQUESTS = 429;

    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    /** @test */
    public function getting_all_breach_sites_returns_a_collection(): void
    {
        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andReturn(new Response(HttpResponse::HTTP_OK, [], self::mockBreachList()));

        $breach = new Breach(new HibpHttp(client: $client));
        $breaches = $breach->getAllBreachSites();

        self::assertSame(HttpResponse::HTTP_OK, $breach->statusCode);
        self::assertCount(8, $breaches);
        self::assertInstanceOf(BreachSiteEntity::class, $breaches->first());
    }

    /** @test */
    public function getting_all_breach_sites_with_invalid_request_throws_request_exception(): void
    {
        $this->expectException(RequestException::class);

        $request = Mockery::mock(Request::class);

        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andThrow(
                new RequestException(
                    message: 'message',
                    request: $request
                ),
            );

        $breach = new Breach(new HibpHttp(client: $client));
        $breach->getAllBreachSites();
    }

    /** @test */
    public function getting_all_filtered_breach_sites_returns_a_valid_collection(): void
    {
        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andReturn(new Response(HttpResponse::HTTP_OK, [], self::mockBreachList()));

        $breach = new Breach(new HibpHttp(client: $client));
        $breaches = $breach->getAllBreachSites(domainFilter: 'adobe.com');

        self::assertSame(HttpResponse::HTTP_OK, $breach->statusCode);
        self::assertCount(8, $breaches);
        self::assertInstanceOf(BreachSiteEntity::class, $breaches->first());
    }

    /** @test */
    public function successful_breach_lookup_returns_a_breach_site_entity(): void
    {
        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andReturn(new Response(HttpResponse::HTTP_OK, [], self::mockSingleAccount()));

        $breach = (new Breach(new HibpHttp(client: $client)))->getBreach(account: '000webhost');

        self::assertNotEmpty($breach->title);
        self::assertNotEmpty($breach->name);
        self::assertNotEmpty($breach->domain);
        self::assertNotEmpty($breach->breachDate);
        self::assertNotEmpty($breach->addedDate);
        self::assertNotEmpty($breach->modifiedDate);
        self::assertNotEmpty($breach->pwnCount);
        self::assertNotEmpty($breach->description);
        self::assertNotEmpty($breach->dataClasses);
        self::assertIsBool($breach->verified);
        self::assertIsBool($breach->fabricated);
        self::assertIsBool($breach->sensitive);
        self::assertIsBool($breach->retired);
        self::assertIsBool($breach->spamList);
        self::assertNotEmpty($breach->logoPath);
    }

    /** @test */
    public function invalid_breach_data_throws_runtime_exception(): void
    {
        $this->expectException(RuntimeException::class);

        new BreachSiteEntity(data: null);
    }

    /** @test */
    public function invalid_breach_lookup_throws_a_guzzle_client_exception(): void
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
            ->andThrow(
                new ClientException(
                    message: 'message',
                    request: Mockery::mock(Request::class),
                    response: $mockedResponse,
                ),
            );

        (new Breach(new HibpHttp(client: $client)))->getBreach(account: '&&');
    }

    /** @test */
    public function unsuccessful_breach_lookup_throws_a_breach_not_found_exception(): void
    {
        $this->expectException(BreachNotFoundException::class);

        $mockedResponse = Mockery::mock(Response::class);
        $mockedResponse
            ->expects('getStatusCode')
            ->once()
            ->andReturn(HttpResponse::HTTP_NOT_FOUND);

        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andThrow(
                new ClientException(
                    message: 'message',
                    request: Mockery::mock(Request::class),
                    response: $mockedResponse,
                ),
            );

        $breach = new Breach(new HibpHttp(client: $client));
        $breach->getBreach(account: '&&');
    }

    /** @test */
    public function invalid_lookup_request_throws_a_breach_not_found_exception(): void
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
            ->andThrow(
                new ClientException(
                    message: 'message',
                    request: Mockery::mock(Request::class),
                    response: $mockedResponse,
                ),
            );

        $breach = new Breach(new HibpHttp(client: $client));
        $breach->getBreach(account: '&&');
    }

    /** @test */
    public function getting_all_dataclasses_returns_a_collection(): void
    {
        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andReturn(new Response(HttpResponse::HTTP_OK, [], self::mockDataClasses()));

        $breach = new Breach(new HibpHttp(client: $client));
        $dataClasses = $breach->getAllDataClasses();

        self::assertSame(HttpResponse::HTTP_OK, $breach->statusCode);
        self::assertSame('Account balances', $dataClasses->first());
    }

    /** @test */
    public function invalid_request_for_dataclasses_throws_a_client_exception(): void
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
            ->andThrow(
                new ClientException(
                    message: 'message',
                    request: Mockery::mock(Request::class),
                    response: $mockedResponse,
                ),
            );

        $breach = new Breach(new HibpHttp(null, $client));
        $breach->getAllDataClasses();
    }

    /** @test */
    public function getting_breach_data_for_account_returns_a_valid_collection(): void
    {
        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andReturn(new Response(HttpResponse::HTTP_OK, [], self::mockBreachList()));

        $breach = new Breach(new HibpHttp(client: $client));
        $breaches = $breach->getBreachedAccount(emailAddress: 'test@example.com');

        self::assertSame(HttpResponse::HTTP_OK, $breach->statusCode);
        self::assertCount(8, $breaches);
        self::assertInstanceOf(BreachSiteEntity::class, $breaches->first());
    }

    /** @test */
    public function getting_breach_data_for_an_invalid_account_throws_a_breach_not_found_exception(): void
    {
        $this->expectException(BreachNotFoundException::class);

        $mockedResponse = Mockery::mock(Response::class);
        $mockedResponse
            ->expects('getStatusCode')
            ->once()
            ->andReturn(HttpResponse::HTTP_NOT_FOUND);

        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andThrow(
                new ClientException(
                    message: 'message',
                    request: Mockery::mock(Request::class),
                    response: $mockedResponse,
                ),
            );

        $breach = new Breach(new HibpHttp(client: $client));
        $breach->getBreachedAccount(emailAddress: 'invalid_email_address');
    }

    /** @test */
    public function invalid_request_for_getting_breach_data_throws_a_request_exception(): void
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
            ->andThrow(
                new ClientException(
                    message: 'message',
                    request: Mockery::mock(Request::class),
                    response: $mockedResponse,
                ),
            );

        $breach = new Breach(new HibpHttp(client: $client));
        $breach->getBreachedAccount(emailAddress: 'invalid_email_address');
    }

    /** @test */
    public function invalid_lookup_for_getting_breach_data_throws_a_client_exception(): void
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
            ->andThrow(
                new ClientException(
                    message: 'message',
                    request: Mockery::mock(Request::class),
                    response: $mockedResponse,
                ),
            );

        $breach = new Breach(new HibpHttp(client: $client));
        $breach->getBreachedAccount(emailAddress: 'invalid_email_address');
    }

    /** @test */
    public function getting_truncated_breached_accounts_returns_a_collection_of_breach_site_truncated_entities(): void
    {
        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andReturn(new Response(HttpResponse::HTTP_OK, [], self::mockBreachList()));

        $breach = new Breach(new HibpHttp(client: $client));
        $breaches = $breach->getBreachedAccountTruncated(emailAddress: 'test@example.com');

        self::assertSame(HttpResponse::HTTP_OK, $breach->statusCode);
        self::assertCount(8, $breaches);
        self::assertInstanceOf(BreachSiteTruncatedEntity::class, $breaches->first());
    }

    /** @test */
    public function getting_missing_truncated_breached_accounts_throws_breach_not_found_exception(): void
    {
        $this->expectException(BreachNotFoundException::class);

        $mockedResponse = Mockery::mock(Response::class);
        $mockedResponse
            ->expects('getStatusCode')
            ->once()
            ->andReturn(HttpResponse::HTTP_NOT_FOUND);

        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andThrow(
                new ClientException(
                    message: 'message',
                    request: Mockery::mock(Request::class),
                    response: $mockedResponse,
                ),
            );

        $breach = new Breach(new HibpHttp(client: $client));
        $breach->getBreachedAccountTruncated(emailAddress: 'test@example.com');
    }

    /** @test */
    public function invalid_request_for_truncated_breached_accounts_throws_request_exception(): void
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
            ->andThrow(
                new ClientException(
                    message: 'message',
                    request: Mockery::mock(Request::class),
                    response: $mockedResponse,
                ),
            );

        $breach = new Breach(new HibpHttp(client: $client));
        $breach->getBreachedAccountTruncated(emailAddress: 'test@example.com');
    }

    /** @test */
    public function invalid_lookup_for_truncated_breached_accounts_throws_client_exception(): void
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
            ->andThrow(
                new ClientException(
                    message: 'message',
                    request: Mockery::mock(Request::class),
                    response: $mockedResponse,
                ),
            );

        $breach = new Breach(new HibpHttp(client: $client));
        $breach->getBreachedAccountTruncated(emailAddress: 'test@example.com');
    }

    /** @test */
    public function getting_filtered_breached_account_returns_a_collection_of_breach_site_entities(): void
    {
        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andReturn(new Response(HttpResponse::HTTP_OK, [], self::mockBreachList()));

        $breach = new Breach(new HibpHttp(client: $client));
        $breaches = $breach->getBreachedAccount(
            emailAddress: 'test@example.com',
            includeUnverified: true,
            domainFilter: 'adobe.com',
        );

        self::assertSame(HttpResponse::HTTP_OK, $breach->statusCode);
        self::assertCount(8, $breaches);
        self::assertInstanceOf(BreachSiteEntity::class, $breaches->first());
    }

    /** @test */
    public function getting_filtered_truncated_breached_accounts_returns_a_collection_of_breach_site_truncated_entities(): void
    {
        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andReturn(new Response(HttpResponse::HTTP_OK, [], self::mockBreachList()));

        $breach = new Breach(new HibpHttp(client: $client));
        $breaches = $breach->getBreachedAccountTruncated(
            emailAddress: 'test@example.com',
            domainFilter: 'adobe.com',
        );

        $breachEntity = $breaches->first();

        self::assertSame(HttpResponse::HTTP_OK, $breach->statusCode);
        self::assertCount(8, $breaches);
        self::assertInstanceOf(BreachSiteTruncatedEntity::class, $breachEntity);
        self::assertSame('000webhost', $breachEntity->name);
    }

    private static function mockBreachList(): string
    {
        $data = file_get_contents(sprintf('%s/_responses/breaches/breaches.json', __DIR__));

        return (false !== $data) ? $data : '[]';
    }

    private static function mockSingleAccount(): string
    {
        $data = file_get_contents(sprintf('%s/_responses/breaches/single_account_breach.json', __DIR__));

        return (false !== $data) ? $data : '{}';
    }

    private static function mockDataClasses(): string
    {
        $data = file_get_contents(sprintf('%s/_responses/breaches/dataclasses.json', __DIR__));

        return (false !== $data) ? $data : '[]';
    }
}
