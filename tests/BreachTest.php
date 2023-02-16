<?php
/**
 * Breach tests
 *
 * @author Ian <ian.h@digiserv.net>
 * @since 04/03/2018
 */

namespace Icawebdesign\Hibp\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Icawebdesign\Hibp\Breach\Breach;
use Icawebdesign\Hibp\Breach\BreachSiteEntity;
use Icawebdesign\Hibp\Breach\BreachSiteTruncatedEntity;
use Icawebdesign\Hibp\Exception\BreachNotFoundException;
use Icawebdesign\Hibp\HibpHttp;
use Mockery;
use PHPUnit\Framework\TestCase;

class BreachTest extends TestCase
{
    /** @var string */
    protected string $apiKey = '';

    /** @var Breach */
    protected Breach $breach;

    protected const TOO_MANY_REQUESTS = 429;

    public function setUp(): void
    {
        parent::setUp();
        $apiKey = file_get_contents(sprintf('%s/../api.key', __DIR__));

        if (false !== $apiKey) {
            $this->apiKey = $apiKey;
        }
    }

    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    /** @test */
    public function gettingAllBreachSitesReturnsACollection(): void
    {
        $client = Mockery::mock(Client::class);
        $client->allows([
            'request' => new Response(200, [], self::mockBreachList()),
        ]);

        $breach = new Breach(new HibpHttp(null, $client));
        $breaches = $breach->getAllBreachSites();

        self::assertSame(200, $breach->getStatusCode());
        self::assertGreaterThan(0, $breaches->count());
        self::assertInstanceOf(BreachSiteEntity::class, $breaches->first());
    }

    /** @test */
    public function gettingAllBreachSitesWithInvalidRequestThrowsRequestException(): void
    {
        $this->expectException(RequestException::class);

        $request = Mockery::mock(Request::class);

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('request')
            ->andThrow(new RequestException('', $request));

        $breach = new Breach(new HibpHttp(null, $client));
        $breach->getAllBreachSites();
    }

    /** @test */
    public function gettingAllFilteredBreachSitesReturnsAValidCollection(): void
    {
        $client = Mockery::mock(Client::class);
        $client->allows([
            'request' => new Response(200, [], self::mockBreachList()),
        ]);

        $breach = new Breach(new HibpHttp(null, $client));
        $breaches = $breach->getAllBreachSites('adobe.com');

        self::assertSame(200, $breach->getStatusCode());
        self::assertGreaterThan(0, $breaches->count());
        self::assertInstanceOf(BreachSiteEntity::class, $breaches->first());
    }

    /** @test */
    public function successfulBreachLookupReturnsABreachSiteEntity(): void
    {
        $client = Mockery::mock(Client::class);
        $client->allows([
            'request' => new Response(200, [], self::mockSingleAccount()),
        ]);

        $breach = new Breach(new HibpHttp(null, $client));
        $breachedAccount = $breach->getBreach('000webhost');

        self::assertNotEmpty($breachedAccount->getTitle());
        self::assertNotEmpty($breachedAccount->getName());
        self::assertNotEmpty($breachedAccount->getDomain());
        self::assertNotEmpty($breachedAccount->getBreachDate());
        self::assertNotEmpty($breachedAccount->getAddedDate());
        self::assertNotEmpty($breachedAccount->getModifiedDate());
        self::assertNotEmpty($breachedAccount->getPwnCount());
        self::assertNotEmpty($breachedAccount->getDescription());
        self::assertNotEmpty($breachedAccount->getDataClasses());
        self::assertIsBool($breachedAccount->isVerified());
        self::assertIsBool($breachedAccount->isFabricated());
        self::assertIsBool($breachedAccount->isSensitive());
        self::assertIsBool($breachedAccount->isRetired());
        self::assertIsBool($breachedAccount->isSpamList());
        self::assertIsBool($breachedAccount->isMalware());
        self::assertNotEmpty($breachedAccount->getLogoPath());
    }

    /** @test */
    public function unsuccessfulBreachLookupThrowsABreachNotFoundException(): void
    {
        $this->expectException(BreachNotFoundException::class);

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('request')
            ->andThrow(new BreachNotFoundException());

        $breach = new Breach(new HibpHttp(null, $client));

        $breach->getBreach('&&');
    }

    /** @test */
    public function gettingAllDataclassesReturnsACollection(): void
    {
        $client = Mockery::mock(Client::class);
        $client->allows([
            'request' => new Response(200, [], self::mockDataClasses()),
        ]);

        $breach = new Breach(new HibpHttp(null, $client));
        $dataClasses = $breach->getAllDataClasses();

        self::assertSame(200, $breach->getStatusCode());
        self::assertSame('Account balances', $dataClasses->first());
    }

    /** @test */
    public function gettingBreachDataForAccountReturnsAValidCollection(): void
    {
        $client = Mockery::mock(Client::class);
        $client->allows([
            'request' => new Response(200, [], self::mockBreachList()),
        ]);

        $breach = new Breach(new HibpHttp(null, $client));
        $breaches = $breach->getBreachedAccount('test@example.com', false);

        self::assertSame(200, $breach->getStatusCode());
        self::assertGreaterThan(0, $breaches->count());
        self::assertInstanceOf(BreachSiteEntity::class, $breaches->first());
    }

    /** @test */
    public function gettingBreachDataForAnInvalidAccountThrowsABreachNotFoundException(): void
    {
        $this->expectException(BreachNotFoundException::class);

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('request')
            ->andThrow(new BreachNotFoundException());

        $breach = new Breach(new HibpHttp(null, $client));
        $breach->getBreachedAccount('invalid_email_address');
    }

    /** @test */
    public function gettingTruncatedBreachedAccountsReturnsACollectionOfBreachSiteTruncatedEntities(): void
    {
        $client = Mockery::mock(Client::class);
        $client->allows([
            'request' => new Response(200, [], self::mockBreachList()),
        ]);

        $breach = new Breach(new HibpHttp(null, $client));
        $breaches = $breach->getBreachedAccountTruncated('test@example.com', false);

        self::assertSame(200, $breach->getStatusCode());
        self::assertGreaterThan(0, $breaches->count());
        self::assertInstanceOf(BreachSiteTruncatedEntity::class, $breaches->first());
    }

    /** @test */
    public function gettingFilteredBreachedAccountReturnsACollectionOfBreachSiteEntities(): void
    {
        $client = Mockery::mock(Client::class);
        $client->allows([
            'request' => new Response(200, [], self::mockBreachFilteredList()),
        ]);

        $breach = new Breach(new HibpHttp(null, $client));
        $breaches = $breach->getBreachedAccount(
            'test@example.com',
            true,
            'adobe.com'
        );

        $breachEntity = $breaches->first();

        self::assertSame(200, $breach->getStatusCode());
        self::assertCount(1, $breaches);
        self::assertInstanceOf(BreachSiteEntity::class, $breachEntity);
        self::assertSame('adobe.com', $breachEntity->getDomain());
    }

    /** @test */
    public function gettingFilteredTruncatedBreachedAccountsReturnsACollectionOfBreachSiteTruncatedEntities(): void
    {
        $client = Mockery::mock(Client::class);
        $client->allows([
            'request' => new Response(200, [], self::mockBreachFilteredList()),
        ]);

        $breach = new Breach(new HibpHttp(null, $client));
        $breaches = $breach->getBreachedAccountTruncated(
            'test@example.com',
            false,
            'adobe.com'
        );

        $breachEntity = $breaches->first();

        self::assertSame(200, $breach->getStatusCode());
        self::assertCount(1, $breaches);
        self::assertInstanceOf(BreachSiteTruncatedEntity::class, $breachEntity);
        self::assertSame('Adobe', $breachEntity->getName());
    }

    private static function mockBreachList(): string
    {
        $data = file_get_contents(sprintf('%s/_responses/breaches/breaches.json', __DIR__));

        return (false !== $data) ? $data : '[]';
    }

    private static function mockBreachFilteredList(): string
    {
        $data = file_get_contents(sprintf('%s/_responses/breaches/breaches_domain_filter.json', __DIR__));

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
