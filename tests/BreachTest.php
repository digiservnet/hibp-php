<?php
/**
 * Breach tests
 *
 * @author Ian <ian@ianh.io>
 * @since 04/03/2018
 */

namespace Icawebdesign\Hibp\Tests;

use Icawebdesign\Hibp\Breach\Breach;
use Icawebdesign\Hibp\Breach\BreachSiteEntity;
use Icawebdesign\Hibp\Breach\BreachSiteTruncatedEntity;
use Icawebdesign\Hibp\Exception\BreachNotFoundException;
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

        $this->breach = new Breach($this->apiKey);
    }

    protected function delay(int $microseconds = 1600000): void
    {
        usleep($microseconds);
    }

    /** @test */
    public function gettingAllBreachSitesReturnsACollection(): void
    {
        $this->delay();
        $breaches = $this->breach->getAllBreachSites();

        self::assertSame(200, $this->breach->getStatusCode());
        self::assertGreaterThan(0, $breaches->count());
        self::assertInstanceOf(BreachSiteEntity::class, $breaches->first());
    }

    /** @test */
    public function gettingAllFilteredBreachSitesReturnsAValidCollection(): void
    {
        $this->delay();
        $breaches = $this->breach->getAllBreachSites('adobe.com');

        self::assertSame(200, $this->breach->getStatusCode());
        self::assertGreaterThan(0, $breaches->count());
        self::assertInstanceOf(BreachSiteEntity::class, $breaches->first());
    }

    /** @test */
    public function successfulBreachLookupReturnsABreachSiteEntity(): void
    {
        $this->delay();
        $breachedAccount = $this->breach->getBreach('000webhost');

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
        self::assertNotEmpty($breachedAccount->getLogoPath());
    }

    /** @test */
    public function unsuccessfulBreachLookupThrowsABreachNotFoundException(): void
    {
        $this->delay();
        $this->expectException(BreachNotFoundException::class);

        $this->breach->getBreach('&&');
    }

    /** @test */
    public function gettingAllDataclassesReturnsACollection(): void
    {
        $this->delay();
        $dataClasses = $this->breach->getAllDataClasses();
        self::assertSame(200, $this->breach->getStatusCode());
    }

    /** @test */
    public function gettingBreachDataForAccountReturnsAValidCollection(): void
    {
        $this->delay();
        $breaches = $this->breach->getBreachedAccount('test@example.com', false);

        self::assertSame(200, $this->breach->getStatusCode());
        self::assertGreaterThan(0, $breaches->count());
        self::assertInstanceOf(BreachSiteEntity::class, $breaches->first());
    }

    /** @test */
    public function gettingBreachDataForAnInvalidAccountThrowsABreachNotFoundException(): void
    {
        $this->delay();
        $this->expectException(BreachNotFoundException::class);
        $this->breach->getBreachedAccount('invalid_email_address');
    }

    /** @test */
    public function gettingTruncatedBreachedAccountsReturnsACollectionOfBreachSiteTruncatedEntities(): void
    {
        $this->delay();
        $breaches = $this->breach->getBreachedAccountTruncated('test@example.com', false);

        self::assertSame(200, $this->breach->getStatusCode());
        self::assertGreaterThan(0, $breaches->count());
        self::assertInstanceOf(BreachSiteTruncatedEntity::class, $breaches->first());
    }

    /** @test */
    public function gettingFilteredBreachedAccountReturnsACollectionOfBreachSiteEntities(): void
    {
        $this->delay();
        $breaches = $this->breach->getBreachedAccount(
            'test@example.com',
            true,
            'adobe.com'
        );

        self::assertSame(200, $this->breach->getStatusCode());
        self::assertGreaterThan(0, $breaches->count());
        self::assertInstanceOf(BreachSiteEntity::class, $breaches->first());
    }

    /** @test */
    public function gettingFilteredTruncatedBreachedAccountsReturnsACollectionOfBreachSiteTruncatedEntities(): void
    {
        $this->delay();
        $breaches = $this->breach->getBreachedAccountTruncated(
            'test@example.com',
            false,
            'adobe.com'
        );

        self::assertSame(200, $this->breach->getStatusCode());
        self::assertGreaterThan(0, $breaches->count());
        self::assertInstanceOf(BreachSiteTruncatedEntity::class, $breaches->first());
    }
}
