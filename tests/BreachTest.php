<?php
/**
 * Breach tests
 *
 * @author Ian <ian@ianh.io>
 * @since 04/03/2018
 */

use Icawebdesign\Hibp\Breach\Breach;
use Icawebdesign\Hibp\Breach\BreachSiteEntity;
use PHPUnit\Framework\TestCase;

class BreachTest extends TestCase
{
    /** @var Breach */
    protected $breach;

    protected const TOO_MANY_REQUESTS = 429;

    public function setUp()
    {
        parent::setUp();
        $this->breach = new Breach();
    }

    public function tearDown()
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
        $this->assertAttributeNotEmpty('title', $breachedAccount);
        $this->assertAttributeNotEmpty('name', $breachedAccount);
        $this->assertAttributeNotEmpty('domain', $breachedAccount);
        $this->assertAttributeNotEmpty('breachDate', $breachedAccount);
        $this->assertAttributeNotEmpty('addedDate', $breachedAccount);
        $this->assertAttributeNotEmpty('modifiedDate', $breachedAccount);
        $this->assertAttributeNotEmpty('pwnCount', $breachedAccount);
        $this->assertAttributeNotEmpty('description', $breachedAccount);
        $this->assertAttributeNotEmpty('dataClasses', $breachedAccount);
        $this->assertAttributeInternalType('bool', 'verified', $breachedAccount);
        $this->assertAttributeInternalType('bool', 'fabricated', $breachedAccount);
        $this->assertAttributeInternalType('bool', 'sensitive', $breachedAccount);
        $this->assertAttributeInternalType('bool', 'retired', $breachedAccount);
        $this->assertAttributeInternalType('bool', 'spamList', $breachedAccount);
        $this->assertAttributeNotEmpty('logoType', $breachedAccount);
    }

    /** @test */
    public function unsuccessfulBreachLookupThrowsAnException()
    {
        $this->expectException(\GuzzleHttp\Exception\GuzzleException::class);

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
    public function gettingBreachDataForAnInvalidAccountThrowsAnException()
    {
        $this->expectException(\GuzzleHttp\Exception\GuzzleException::class);
        $this->breach->getBreachedAccount('invalid_email_address');
    }
}
