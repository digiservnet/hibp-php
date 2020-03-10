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
use Tightenco\Collect\Support\Collection;

class BreachTest extends TestCase
{
    protected $apiKey = '';

    /** @var Breach */
    protected $breach;

    protected const TOO_MANY_REQUESTS = 429;

    public function setUp(): void
    {
        parent::setUp();
        $this->apiKey = file_get_contents(sprintf('%s/../api.key', __DIR__));
        $this->breach = new Breach($this->apiKey);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->breach = null;
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

        $this->assertSame(200, $this->breach->getStatusCode());
        $this->assertGreaterThan(0, $breaches->count());
        $this->assertInstanceOf(BreachSiteEntity::class, $breaches->first());
    }

    /** @test */
    public function gettingAllFilteredBreachSitesReturnsAValidCollection(): void
    {
        $this->delay();
        $breaches = $this->breach->getAllBreachSites('adobe.com');

        $this->assertSame(200, $this->breach->getStatusCode());
        $this->assertGreaterThan(0, $breaches->count());
        $this->assertInstanceOf(BreachSiteEntity::class, $breaches->first());
    }

    /** @test */
    public function successfulBreachLookupReturnsABreachSiteEntity(): void
    {
        $this->delay();
        $breachedAccount = $this->breach->getBreach('000webhost');

        $this->assertNotEmpty($breachedAccount->getTitle());
        $this->assertNotEmpty($breachedAccount->getName());
        $this->assertNotEmpty($breachedAccount->getDomain());
        $this->assertNotEmpty($breachedAccount->getBreachDate());
        $this->assertNotEmpty($breachedAccount->getAddedDate());
        $this->assertNotEmpty($breachedAccount->getModifiedDate());
        $this->assertNotEmpty($breachedAccount->getPwnCount());
        $this->assertNotEmpty($breachedAccount->getDescription());
        $this->assertNotEmpty($breachedAccount->getDataClasses());
        $this->assertIsBool($breachedAccount->isVerified());
        $this->assertIsBool($breachedAccount->isFabricated());
        $this->assertIsBool($breachedAccount->isSensitive());
        $this->assertIsBool($breachedAccount->isRetired());
        $this->assertIsBool($breachedAccount->isSpamList());
        $this->assertNotEmpty($breachedAccount->getLogoPath());
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
        $this->assertSame(200, $this->breach->getStatusCode());
        $this->assertInstanceOf(Collection::class, $dataClasses);
    }

    /** @test */
    public function gettingBreachDataForAccountReturnsAValidCollection(): void
    {
        $this->delay();
        $breaches = $this->breach->getBreachedAccount('test@example.com', false);

        $this->assertSame(200, $this->breach->getStatusCode());
        $this->assertGreaterThan(0, $breaches->count());
        $this->assertInstanceOf(BreachSiteEntity::class, $breaches->first());
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

        $this->assertSame(200, $this->breach->getStatusCode());
        $this->assertInstanceOf(Collection::class, $breaches);
        $this->assertGreaterThan(0, $breaches->count());
        $this->assertInstanceOf(BreachSiteTruncatedEntity::class, $breaches->first());
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

        $this->assertSame(200, $this->breach->getStatusCode());
        $this->assertGreaterThan(0, $breaches->count());
        $this->assertInstanceOf(BreachSiteEntity::class, $breaches->first());
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

        $this->assertSame(200, $this->breach->getStatusCode());
        $this->assertGreaterThan(0, $breaches->count());
        $this->assertInstanceOf(BreachSiteTruncatedEntity::class, $breaches->first());
    }
}
