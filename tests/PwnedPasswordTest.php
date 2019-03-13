<?php
/**
 * PwnedPassword tests
 *
 * @author Ian <ian@ianh.io>
 * @since 28/02/2018
 */

namespace Icawebdesign\Test;

use Icawebdesign\Hibp\Password\PwnedPassword;
use PHPUnit\Framework\TestCase;

class PwnedPasswordTest extends TestCase
{
    /** @var PwnedPassword */
    protected $pwnedPassword;

    public function setUp(): void
    {
        parent::setUp();
        $this->pwnedPassword = new PwnedPassword();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->pwnedPassword = null;
    }

    /** @test */
    public function successfulRangeLookupReturnsAPositiveInteger()
    {
        $response = $this->pwnedPassword->range('5baa6', '5BAA61E4C9B93F3F0682250B6CF8331B7EE68FD8');

        $this->assertEquals(200, $this->pwnedPassword->getStatusCode());
        $this->assertIsInt($response);
        $this->assertGreaterThan(0, $response);
    }

    /** @test */
    public function failedRangeLookupReturnsZero()
    {
        $response = $this->pwnedPassword->range('00000', '0000000000000000000000000000000000000000');

        $this->assertEquals(200, $this->pwnedPassword->getStatusCode());
        $this->assertEquals(0, $response);
    }

    /** @test */
    public function successfulRangeDataLookupReturnsACollection()
    {
        $response = $this->pwnedPassword->rangeData('5baa6', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8');

        $this->assertEquals(200, $this->pwnedPassword->getStatusCode());
        $this->assertInstanceOf(\Tightenco\Collect\Support\Collection::class, $response);
    }

    /** @test */
    public function invalidRangeThrowsARequestException()
    {
        $this->expectException(\GuzzleHttp\Exception\RequestException::class);

        $this->pwnedPassword->range('&&', '&&');
    }

    /** @test */
    public function invalidRangeDataThrowsAnException()
    {
        $this->expectException(\GuzzleHttp\Exception\RequestException::class);

        $this->pwnedPassword->rangeData('&&', '&&');
    }
}
