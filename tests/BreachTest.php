<?php

namespace Icawebdesign\Hibp\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Icawebdesign\Hibp\Breach\Breach;
use Icawebdesign\Hibp\Breach\BreachSiteEntity;
use Icawebdesign\Hibp\Breach\BreachSiteTruncatedEntity;
use Icawebdesign\Hibp\Exception\BreachNotFoundException;
use Icawebdesign\Hibp\HibpHttp;
use Illuminate\Support\Collection;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;
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

    #[Test]
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

    #[Test]
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
                    request: $request,
                ),
            );

        $breach = new Breach(new HibpHttp(client: $client));
        $breach->getAllBreachSites();
    }

    #[Test]
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

    #[Test]
    public function successful_breach_lookup_returns_a_breach_site_entity(): void
    {
        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andReturn(new Response(HttpResponse::HTTP_OK, [], self::mockSingleAccount()));

        $breach = (new Breach(new HibpHttp(client: $client)))->getBreach(account: 'adobe');

        $dataClasses = Collection::make([
            'Email addresses',
            'Password hints',
            'Passwords',
            'Usernames',
        ]);

        self::assertSame('Adobe', $breach->title);
        self::assertSame('Adobe', $breach->name);
        self::assertSame('adobe.com', $breach->domain);
        self::assertSame('2013-10-04', $breach->breachDate->toDateString());
        self::assertSame('2013-12-04T00:00:00Z', $breach->addedDate->toIso8601ZuluString());
        self::assertSame('2022-05-15T23:52:49Z', $breach->modifiedDate->toIso8601ZuluString());
        self::assertSame(152445165, $breach->pwnCount);
        self::assertSame(
            'In October 2013, 153 million Adobe accounts were breached with each containing an internal ID, username, email, <em>encrypted</em> password and a password hint in plain text. The password cryptography was poorly done and many were quickly resolved back to plain text. The unencrypted hints also <a href="http://www.troyhunt.com/2013/11/adobe-credentials-and-serious.html" target="_blank" rel="noopener">disclosed much about the passwords</a> adding further to the risk that hundreds of millions of Adobe customers already faced.',
            $breach->description,
        );
        self::assertEquals($dataClasses, $breach->dataClasses);
        self::assertTrue($breach->verified);
        self::assertFalse($breach->fabricated);
        self::assertFalse($breach->sensitive);
        self::assertFalse($breach->retired);
        self::assertFalse($breach->spamList);
        self::assertFalse($breach->malware);
        self::assertFalse($breach->subscriptionFree);
        self::assertSame(
            'https://haveibeenpwned.com/Content/Images/PwnedLogos/Adobe.png',
            $breach->logoPath,
        );
    }

    #[Test]
    public function invalid_breach_data_throws_runtime_exception(): void
    {
        $this->expectException(RuntimeException::class);

        new BreachSiteEntity(data: null);
    }

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
    public function getting_filtered_breached_account_returns_a_collection_of_breach_site_entities(): void
    {
        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andReturn(new Response(HttpResponse::HTTP_OK, [], self::mockBreachFilteredList()));

        $breach = new Breach(new HibpHttp(client: $client));
        $breaches = $breach->getBreachedAccount(
            emailAddress: 'test@example.com',
            includeUnverified: true,
            domainFilter: 'adobe.com',
        );

        $breachEntity = $breaches->first();

        self::assertSame(HttpResponse::HTTP_OK, $breach->statusCode);
        self::assertCount(1, $breaches);
        self::assertInstanceOf(BreachSiteEntity::class, $breachEntity);
        self::assertSame('adobe.com', $breachEntity->domain);
    }

    #[Test]
    public function getting_filtered_truncated_breached_accounts_returns_a_collection_of_breach_site_truncated_entities(
    ): void
    {
        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andReturn(new Response(HttpResponse::HTTP_OK, [], self::mockBreachFilteredList()));

        $breach = new Breach(new HibpHttp(client: $client));
        $breaches = $breach->getBreachedAccountTruncated(
            emailAddress: 'test@example.com',
            domainFilter: 'adobe.com',
        );

        $breachEntity = $breaches->first();

        self::assertSame(HttpResponse::HTTP_OK, $breach->statusCode);
        self::assertCount(1, $breaches);
        self::assertInstanceOf(BreachSiteTruncatedEntity::class, $breachEntity);
        self::assertSame('Adobe', $breachEntity->name);
        self::assertSame('Adobe', $breachEntity->title);
    }

    #[Test]
    public function invalid_truncated_breach_data_throws_runtime_exception(): void
    {
        $this->expectException(RuntimeException::class);

        new BreachSiteTruncatedEntity(data: null);
    }

    #[Test]
    public function it_can_get_the_latest_breach(): void
    {
        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andReturn(new Response(HttpResponse::HTTP_OK, [], self::mockSingleAccount()));

        $breach = (new Breach(new HibpHttp(client: $client)))->getLatestBreach();

        $dataClasses = Collection::make([
            'Email addresses',
            'Password hints',
            'Passwords',
            'Usernames',
        ]);

        self::assertSame('Adobe', $breach->title);
        self::assertSame('Adobe', $breach->name);
        self::assertSame('adobe.com', $breach->domain);
        self::assertSame('2013-10-04', $breach->breachDate->toDateString());
        self::assertSame('2013-12-04T00:00:00Z', $breach->addedDate->toIso8601ZuluString());
        self::assertSame('2022-05-15T23:52:49Z', $breach->modifiedDate->toIso8601ZuluString());
        self::assertSame(152445165, $breach->pwnCount);
        self::assertSame(
            'In October 2013, 153 million Adobe accounts were breached with each containing an internal ID, username, email, <em>encrypted</em> password and a password hint in plain text. The password cryptography was poorly done and many were quickly resolved back to plain text. The unencrypted hints also <a href="http://www.troyhunt.com/2013/11/adobe-credentials-and-serious.html" target="_blank" rel="noopener">disclosed much about the passwords</a> adding further to the risk that hundreds of millions of Adobe customers already faced.',
            $breach->description,
        );
        self::assertEquals($dataClasses, $breach->dataClasses);
        self::assertTrue($breach->verified);
        self::assertFalse($breach->fabricated);
        self::assertFalse($breach->sensitive);
        self::assertFalse($breach->retired);
        self::assertFalse($breach->spamList);
        self::assertFalse($breach->malware);
        self::assertFalse($breach->subscriptionFree);
        self::assertSame(
            'https://haveibeenpwned.com/Content/Images/PwnedLogos/Adobe.png',
            $breach->logoPath,
        );
    }

    #[Test]
    public function invalid_latest_breach_lookup_throws_a_guzzle_client_exception(): void
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

        (new Breach(new HibpHttp(client: $client)))->getLatestBreach();
    }

    #[Test]
    public function unsuccessful_latest_breach_lookup_throws_a_breach_not_found_exception(): void
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

        (new Breach(new HibpHttp(client: $client)))->getLatestBreach();
    }

    #[Test]
    public function invalid_request_for_getting_latest_breach_data_throws_a_request_exception(): void
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

        (new Breach(new HibpHttp(client: $client)))->getLatestBreach();
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

    private static function mockBreachFilteredList(): string
    {
        $data = file_get_contents(sprintf('%s/_responses/breaches/breaches_domain_filter.json', __DIR__));

        return (false !== $data) ? $data : '[]';
    }
}
