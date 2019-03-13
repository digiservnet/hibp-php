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
        $this->breach = null;
    }

    /** @test */
    public function gettingAllBreachesitesReturnsACollection()
    {
        $breaches = $this->breach->getAllBreachSites();

        $this->assertEquals(200, $this->breach->getStatusCode());
        $this->assertInstanceOf(\Tightenco\Collect\Support\Collection::class, $breaches);
        $this->assertGreaterThan(0, $breaches->count());
        $this->assertInstanceOf(BreachSiteEntity::class, $breaches->first());
    }

    /** @test */
    public function successfulBreachLookupReturnsABreachSiteEntity()
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
    public function unsuccessfulBreachLookupThrowsABreachNotFoundException()
    {
        $this->expectException(\Icawebdesign\Hibp\Exception\BreachNotFoundException::class);

        $this->breach->getBreach('&&');
    }

    /** @test */
    public function gettingAllDataclassesReturnsACollection()
    {
        $dataClasses = $this->breach->getAllDataClasses();
        $this->assertEquals(200, $this->breach->getStatusCode());
        $this->assertInstanceOf(\Tightenco\Collect\Support\Collection::class, $dataClasses);
    }

    /** @test */
    public function gettingBreachDataForAccountReturnsACollection()
    {
        $breaches = $this->breach->getBreachedAccount('test@example.com');

        $this->assertEquals(200, $this->breach->getStatusCode());
        $this->assertInstanceOf(\Tightenco\Collect\Support\Collection::class, $breaches);
        $this->assertGreaterThan(0, $breaches->count());
        $this->assertInstanceOf(BreachSiteEntity::class, $breaches->first());
    }

    /** @test */
    public function gettingBreachDataForAnInvalidAccountThrowsABreachNotFoundException()
    {
        $this->expectException(\Icawebdesign\Hibp\Exception\BreachNotFoundException::class);
        $this->breach->getBreachedAccount('invalid_email_address');
    }
}
