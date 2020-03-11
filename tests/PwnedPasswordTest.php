<?php
/**
 * PwnedPassword tests
 *
 * @author Ian <ian@ianh.io>
 * @since 28/02/2018
 */

namespace Icawebdesign\Hibp\Tests;

use GuzzleHttp\Exception\RequestException;
use Icawebdesign\Hibp\Exception\PaddingHashCollisionException;
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

    /** @test */
    public function successfulRangeLookupReturnsAPositiveInteger(): void
    {
        $response = $this->pwnedPassword->rangeFromHash('5BAA61E4C9B93F3F0682250B6CF8331B7EE68FD8');

        $this->assertSame(200, $this->pwnedPassword->getStatusCode());
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
    public function failedRangeLookupReturnsZero(): void
    {
        $response = $this->pwnedPassword->rangeFromHash('0000000000000000000000000000000000000000');

        $this->assertSame(200, $this->pwnedPassword->getStatusCode());
        $this->assertEquals(0, $response);
    }

    /** @test */
    public function successfulRangeDataLookupReturnsAValidCollection(): void
    {
        $response = $this->pwnedPassword->rangeDataFromHash('5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8');

        $this->assertSame(200, $this->pwnedPassword->getStatusCode());
        $this->assertGreaterThan(0, $response->last()['count']);
    }

    /** @test */
    public function successfulPaddedRangeDataLookupReturnsAValidCollectionWithZeroCountElements(): void
    {
        $response = $this->pwnedPassword->paddedRangeDataFromHash('5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8');

        $this->assertSame(200, $this->pwnedPassword->getStatusCode());
        $this->assertSame(0, $response->last()['count']);
    }

    /** @test */
    public function strippedSuccessfulPaddedRangeReturnsAValidCollectionWithoutZeroCountElements(): void
    {
        $hash = '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8';
        $response = $this->pwnedPassword->paddedRangeDataFromHash($hash);

        $this->assertSame(200, $this->pwnedPassword->getStatusCode());
        $this->assertGreaterThan(
            0,
            PwnedPassword::stripZeroMatchesData($response, $hash)->last()
        );
    }

    /** @test */
    public function invalidRangeThrowsARequestException(): void
    {
        $this->expectException(RequestException::class);

        $this->pwnedPassword->rangeFromHash('&&');
    }

    /** @test */
    public function invalidRangeDataThrowsAnException(): void
    {
        $this->expectException(RequestException::class);

        $this->pwnedPassword->rangeDataFromHash('&&');
    }

    /** @test */
    public function strippingPaddedRangeElementMatchingHashThrowsAPaddingHashCollisionException(): void
    {
        $this->expectException(PaddingHashCollisionException::class);

        $hash = '5BAA61E4C9B93F3F0682250B6CF8331B7EE68FD8';
        $response = $this->pwnedPassword->paddedRangeDataFromHash($hash);

        $this->assertSame(200, $this->pwnedPassword->getStatusCode());

        $data[$hash] = $response->first();
        $data[$hash]['hashSnippet'] = $hash;
        $data[$hash]['count'] = 0;

        $this->assertGreaterThan(
            0,
            PwnedPassword::stripZeroMatchesData((new Collection($data)), $hash)->last()
        );
    }
}
