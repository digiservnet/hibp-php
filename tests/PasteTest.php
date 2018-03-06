<?php

use GuzzleHttp\Exception\GuzzleException;
use Icawebdesign\Hibp\Paste\Paste;
use PHPUnit\Framework\TestCase;
use Tightenco\Collect\Support\Collection;

/**
 * Paste tests
 *
 * @author Ian <ian@ianh.io>
 * @since 05/03/2018
 */

class PasteTest extends TestCase
{
    protected $pastes;

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
        $this->pastes = new Paste([
            'api_root' => 'https://haveibeenpwned.com/api/v2',
        ]);
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->pastes = null;
    }

    /** @test */
    public function instance_of_class_should_be_a_Pastes()
    {
        $this->assertInstanceOf(Paste::class, $this->pastes);

        $this->addDelay();
    }

    /** @test */
    public function successful_lookup_should_return_status_code_200()
    {
        try {
            $this->pastes->lookup('test@example.com');
        } catch (GuzzleException $e) {}

        $this->assertEquals(200, $this->pastes->getStatusCode());

        $this->addDelay();
    }

    /** @test */
    public function failed_lookup_should_return_status_code_404()
    {
        try {
            $this->pastes->lookup('test@example.comm');
        } catch (GuzzleException $e) {
            $this->assertEquals(404, $e->getCode());
        }

        $this->addDelay();
    }

    /** @test */
    public function successful_lookup_should_return_a_collection()
    {
        try {
            $data = $this->pastes->lookup('test@example.com');
        } catch (GuzzleException $e) {}

        $this->assertEquals(200, $this->pastes->getStatusCode());
        $this->assertInstanceOf(Collection::class, $data);
        $this->assertGreaterThan(0, $data->count());

        $paste = $data->first();

        $this->assertAttributeNotEmpty('source', $paste);
        $this->assertAttributeNotEmpty('id', $paste);
        $this->assertAttributeNotEmpty('date', $paste);
        $this->assertAttributeNotEmpty('emailCount', $paste);
        $this->assertInstanceOf(Carbon\Carbon::class, $paste->getDate());
        $this->assertInternalType('int', $paste->getEmailCount());
        $this->assertGreaterThan(0, $paste->getEmailCount());

        $this->addDelay();
    }
}
