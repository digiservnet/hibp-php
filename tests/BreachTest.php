<?php
/**
 * Breach tests
 *
 * @author Ian <ian@ianh.io>
 * @since 04/03/2018
 */

use Icawebdesign\Hibp\Breach\Breach;
use GuzzleHttp\Exception\GuzzleException;
use Icawebdesign\Hibp\Breach\BreachSiteEntity;
use PHPUnit\Framework\TestCase;
use Tightenco\Collect\Support\Collection;

class BreachTest extends TestCase
{
    protected $breaches;

    /**
     * Add delay between tests to prevent hitting rate limit
     *
     * @see https://haveibeenpwned.com/API/v2#RateLimiting
     *
     * @param float $delay
     */
    protected function addDelay(float $delay = 1.2)
    {
        usleep($delay * 1000000);
    }

    public function setUp()
    {
        parent::setUp();
        $this->breaches = new Breach([
            'api_root' => 'https://haveibeenpwned.com/api/v2',
        ]);
    }

    public function tearDown()
    {
        $this->breaches = null;
    }

    /** @test */
    public function instance_of_class_should_be_a_Breaches()
    {
        $this->assertInstanceOf(Breach::class, $this->breaches);

        $this->addDelay();
    }

    /** @test */
    public function getting_all_breaches_should_return_status_code_200()
    {
        try {
            $this->breaches->getAllBreachSites();
        } catch (GuzzleException $e) {}

        $this->assertEquals(200, $this->breaches->getStatusCode());

        $this->addDelay();
    }

    /** @test */
    public function getting_all_breachesites_should_return_a_collection()
    {
        try {
            $breaches = $this->breaches->getAllBreachSites();
        } catch (GuzzleException $e) {}

        $this->assertInstanceOf(Collection::class, $breaches);
        $this->assertGreaterThan(0, $breaches->count());
        $this->assertInstanceOf(BreachSiteEntity::class, $breaches->first());

        $this->addDelay();
    }

    /** @test */
    public function successful_breach_lookup_should_return_BreachSiteEntity()
    {
        try {
            $breach = $this->breaches->getBreach('adobe');
        } catch (GuzzleException $e) {
            echo $e->getCode();
        }

        $this->assertInstanceOf(BreachSiteEntity::class, $breach);
        $this->assertAttributeNotEmpty('title', $breach);
        $this->assertAttributeNotEmpty('name', $breach);
        $this->assertAttributeNotEmpty('domain', $breach);
        $this->assertAttributeNotEmpty('breachDate', $breach);
        $this->assertAttributeNotEmpty('addedDate', $breach);
        $this->assertAttributeNotEmpty('modifiedDate', $breach);
        $this->assertAttributeNotEmpty('pwnCount', $breach);
        $this->assertAttributeNotEmpty('description', $breach);
        $this->assertAttributeNotEmpty('dataClasses', $breach);
        $this->assertAttributeInternalType('bool', 'verified', $breach);
        $this->assertAttributeInternalType('bool', 'fabricated', $breach);
        $this->assertAttributeInternalType('bool', 'sensitive', $breach);
        $this->assertAttributeInternalType('bool', 'active', $breach);
        $this->assertAttributeInternalType('bool', 'retired', $breach);
        $this->assertAttributeInternalType('bool', 'spamList', $breach);
        $this->assertAttributeNotEmpty('logoType', $breach);

        $this->addDelay();
    }

    /** @test */
    public function getting_all_dataclasses_should_return_status_code_200()
    {
        try {
            $this->breaches->getAllDataClasses();
        } catch (GuzzleException $e) {}

        $this->assertEquals(200, $this->breaches->getStatusCode());

        $this->addDelay();
    }

    /** @test */
    public function getting_all_dataclasses_should_return_a_collection()
    {
        try {
            $dataClasses = $this->breaches->getAllDataClasses();
        } catch (GuzzleException $e) {}

        $this->assertInstanceOf(Collection::class, $dataClasses);

        $this->addDelay();
    }

    /** @test */
    public function getting_breach_data_for_account_should_return_status_code_200()
    {
        try {
            $this->breaches->getBreachedAccount('test@example.com');
        } catch (GuzzleException $e) {}

        $this->assertEquals(200, $this->breaches->getStatusCode());

        $this->addDelay();
    }

    /** @test */
    public function getting_breach_data_for_account_should_return_a_collection()
    {
        try {
            $breaches = $this->breaches->getBreachedAccount('test@example.com');
        } catch (GuzzleException $e) {
            echo $e->getCode();
        }

        $this->assertEquals(200, $this->breaches->getStatusCode());
        $this->assertInstanceOf(Collection::class, $breaches);
        $this->assertGreaterThan(0, $breaches->count());
        $this->assertInstanceOf(BreachSiteEntity::class, $breaches->first());

        $this->addDelay();
    }

    /** @test */
    public function getting_breach_data_for_unknown_account_should_return_404()
    {
        try {
            $this->breaches->getBreachedAccount('test@example.comm');
        } catch (GuzzleException $e) {
            $this->assertEquals(404, $e->getCode());
        }

        $this->addDelay();
    }
}
