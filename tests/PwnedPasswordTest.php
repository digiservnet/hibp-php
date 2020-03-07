<?php
/**
 * PwnedPassword tests
 *
 * @author Ian <ian@ianh.io>
 * @since 28/02/2018
 */

namespace Icawebdesign\Test;

use GuzzleHttp\Exception\RequestException;
use Icawebdesign\Hibp\Password\PwnedPassword;
use PHPUnit\Framework\TestCase;
use Tightenco\Collect\Support\Collection;

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
        $response = $this->pwnedPassword->rangeFromHash('5BAA61E4C9B93F3F0682250B6CF8331B7EE68FD8');

        $this->assertEquals(200, $this->pwnedPassword->getStatusCode());
        $this->assertIsInt($response);
        $this->assertGreaterThan(0, $response);
    }

    /** @test */
    public function successfulRangeWithPaddingReturnsAPositiveInteger(): void
    {
        $response = $this->pwnedPassword->paddedRangeFromHash('5BAA61E4C9B93F3F0682250B6CF8331B7EE68FD8');

        $this->assertSame(200, $this->pwnedPassword->getStatusCode());
        $this->assertIsInt($response);
        $this->assertGreaterThan(0, $response);
    }

    /** @test */
    public function failedRangeLookupReturnsZero()
    {
        $response = $this->pwnedPassword->rangeFromHash('0000000000000000000000000000000000000000');

        $this->assertEquals(200, $this->pwnedPassword->getStatusCode());
        $this->assertEquals(0, $response);
    }

    /** @test */
    public function successfulRangeDataLookupReturnsACollection()
    {
        $response = $this->pwnedPassword->rangeDataFromHash('5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8');

        $this->assertEquals(200, $this->pwnedPassword->getStatusCode());
        $this->assertInstanceOf(Collection::class, $response);
    }

    /** @test */
    public function invalidRangeThrowsARequestException()
    {
        $this->expectException(RequestException::class);

        $this->pwnedPassword->rangeFromHash('&&');
    }

    /** @test */
    public function invalidRangeDataThrowsAnException()
    {
        $this->expectException(RequestException::class);

        $this->pwnedPassword->rangeDataFromHash('&&');
    }
}
