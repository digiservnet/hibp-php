<?php

namespace Icawebdesign\Test;

use Icawebdesign\Hibp\Paste\Paste;
use Icawebdesign\Hibp\Paste\PasteEntity;
use PHPUnit\Framework\TestCase;

/**
 * Paste tests
 *
 * @author Ian <ian@ianh.io>
 * @since 05/03/2018
 */

class PasteTest extends TestCase
{
    protected $apiKey = '';

    protected $paste;

    public function setUp(): void
    {
        parent::setUp();
        $this->apiKey = file_get_contents(sprintf('%s/../api.key', __DIR__));
        $this->paste = new Paste($this->apiKey);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->paste = null;
    }

    protected function delay(int $microseconds = 1600): void
    {
        usleep($microseconds);
    }

    /** @test */
    public function successfulLookupReturnsACollection()
    {
        $this->delay();
        $pastes = $this->paste->lookup('test@example.com');

        $this->assertEquals(200, $this->paste->getStatusCode());
        $this->assertInstanceOf(\Tightenco\Collect\Support\Collection::class, $pastes);
        $this->assertGreaterThan(0, $pastes->count());

        /** @var PasteEntity $account */
        $account = $pastes->first();

        $this->assertNotEmpty($account->getSource());
        $this->assertNotEmpty($account->getId());
        $this->assertIsInt($account->getEmailCount());
        $this->assertIsString($account->getLink());
        $this->assertIsInt($account->getEmailCount());
        $this->assertGreaterThan(0, $account->getEmailCount());
    }

    /** @test */
    public function invalidLookupThrowsException()
    {
        $this->delay();
        $this->expectException(\GuzzleHttp\Exception\RequestException::class);

        $this->paste->lookup('invalid_email_address');
    }

    /** @test */
    public function notFoundLookupThrowsPasteNotFoundException()
    {
        $this->delay();
        $this->expectException(\Icawebdesign\Hibp\Exception\PasteNotFoundException::class);

        $this->paste->lookup('unknown-address@example.com');
    }
}
