<?php

use Icawebdesign\Hibp\Paste\Paste;
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

    public function setUp()
    {
        parent::setUp();
        $this->paste = new Paste();
    }

    public function tearDown()
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

        $account = $pastes->first();

        $this->assertAttributeNotEmpty('source', $account);
        $this->assertAttributeNotEmpty('id', $account);
        $this->assertAttributeNotEmpty('emailCount', $account);
        $this->assertInternalType('string', $account->getLink());
        $this->assertInternalType('int', $account->getEmailCount());
        $this->assertGreaterThan(0, $account->getEmailCount());
    }

    /** @test */
    public function unsuccessfulLookupThrowsException()
    {
        $this->expectException(\GuzzleHttp\Exception\GuzzleException::class);

        $this->paste->lookup('invalid_email_address');
    }
}
