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
    protected $paste;

    public function setUp(): void
    {
        parent::setUp();
        $this->paste = new Paste();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->paste = null;
    }

    /** @test */
    public function successfulLookupReturnsACollection()
    {
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
        $this->expectException(\GuzzleHttp\Exception\RequestException::class);

        $this->paste->lookup('invalid_email_address');
    }

    /** @test */
    public function notFoundLookupThrowsPasteNotFoundException()
    {
        $this->expectException(\Icawebdesign\Hibp\Exception\PasteNotFoundException::class);

        $this->paste->lookup('unknown-address@example.com');
    }
}
