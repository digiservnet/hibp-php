<?php
/**
 * Breach tests
 *
 * @author Ian <ian@ianh.io>
 * @since 04/03/2018
 */

namespace Icawebdesign\Test;

use Icawebdesign\Hibp\Breach\Breach;
use Icawebdesign\Hibp\Breach\BreachSiteEntity;
use Icawebdesign\Hibp\Breach\BreachSiteTruncatedEntity;
use PHPUnit\Framework\TestCase;

class BreachTest extends TestCase
{
    /** @var Breach */
    protected $breach;

    protected const TOO_MANY_REQUESTS = 429;

    public function setUp(): void
    {
        parent::setUp();
        $this->breach = new Breach();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->breach = null;
    }

    /** @test */
    public function gettingAllBreachSitesReturnsACollection(): void
    {
        $breaches = $this->breach->getAllBreachSites();

        $this->assertEquals(200, $this->breach->getStatusCode());
        $this->assertInstanceOf(\Tightenco\Collect\Support\Collection::class, $breaches);
        $this->assertGreaterThan(0, $breaches->count());
        $this->assertInstanceOf(BreachSiteEntity::class, $breaches->first());
    }

    /** @test */
    public function gettingAllFilteredBreachSitesReturnsACollection(): void
    {
        $breaches = $this->breach->getAllBreachSites('adobe.com');

        $this->assertEquals(200, $this->breach->getStatusCode());
        $this->assertInstanceOf(\Tightenco\Collect\Support\Collection::class, $breaches);
        $this->assertGreaterThan(0, $breaches->count());
        $this->assertInstanceOf(BreachSiteEntity::class, $breaches->first());
    }

    /** @test */
    public function successfulBreachLookupReturnsABreachSiteEntity(): void
    {
        $breachedAccount = $this->breach->getBreach('000webhost');

        $this->assertInstanceOf(BreachSiteEntity::class, $breachedAccount);
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
        $this->expectException(\Icawebdesign\Hibp\Exception\BreachNotFoundException::class);

        $this->breach->getBreach('&&');
    }

    /** @test */
    public function gettingAllDataclassesReturnsACollection(): void
    {
        $dataClasses = $this->breach->getAllDataClasses();
        $this->assertEquals(200, $this->breach->getStatusCode());
        $this->assertInstanceOf(\Tightenco\Collect\Support\Collection::class, $dataClasses);
    }

    /** @test */
    public function gettingBreachDataForAccountReturnsACollection(): void
    {
        $breaches = $this->breach->getBreachedAccount('test@example.com', false);

        $this->assertEquals(200, $this->breach->getStatusCode());
        $this->assertInstanceOf(\Tightenco\Collect\Support\Collection::class, $breaches);
        $this->assertGreaterThan(0, $breaches->count());
        $this->assertInstanceOf(BreachSiteEntity::class, $breaches->first());
    }

    /** @test */
    public function gettingBreachDataForAnInvalidAccountThrowsABreachNotFoundException(): void
    {
        $this->expectException(\Icawebdesign\Hibp\Exception\BreachNotFoundException::class);
        $this->breach->getBreachedAccount('invalid_email_address');
    }

    /** @test */
    public function gettingTruncatedBreachedAccountsReturnsACollectionOfBreachSiteTruncatedEntities(): void
    {
        $breaches = $this->breach->getBreachedAccountTruncated('test@example.com', false);

        $this->assertEquals(200, $this->breach->getStatusCode());
        $this->assertInstanceOf(\Tightenco\Collect\Support\Collection::class, $breaches);
        $this->assertGreaterThan(0, $breaches->count());
        $this->assertInstanceOf(BreachSiteTruncatedEntity::class, $breaches->first());
    }

    /** @test */
    public function gettingFilteredBreachedAccountReturnsACollectionOfBreachSiteEntities(): void
    {
        $breaches = $this->breach->getBreachedAccount(
            'test@example.com',
            true,
            'adobe.com'
        );

        $this->assertEquals(200, $this->breach->getStatusCode());
        $this->assertInstanceOf(\Tightenco\Collect\Support\Collection::class, $breaches);
        $this->assertGreaterThan(0, $breaches->count());
        $this->assertInstanceOf(BreachSiteEntity::class, $breaches->first());
    }

    /** @test */
    public function gettingFilteredTruncatedBreachedAccountsReturnsACollectionOfBreachSiteTruncatedEntities(): void
    {
        $breaches = $this->breach->getBreachedAccountTruncated(
            'test@example.com',
            false,
            'adobe.com'
        );

        $this->assertEquals(200, $this->breach->getStatusCode());
        $this->assertInstanceOf(\Tightenco\Collect\Support\Collection::class, $breaches);
        $this->assertGreaterThan(0, $breaches->count());
        $this->assertInstanceOf(BreachSiteTruncatedEntity::class, $breaches->first());
    }
}
