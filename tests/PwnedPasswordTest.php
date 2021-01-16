<?php
/**
 * PwnedPassword tests
 *
 * @author Ian <ian@ianh.io>
 * @since 28/02/2018
 */

namespace Icawebdesign\Hibp\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Icawebdesign\Hibp\Exception\PaddingHashCollisionException;
use Icawebdesign\Hibp\HibpHttp;
use Icawebdesign\Hibp\Password\PwnedPassword;
use Illuminate\Support\Collection;
use Mockery;
use PHPUnit\Framework\TestCase;

class PwnedPasswordTest extends TestCase
{
    /** @var PwnedPassword */
    protected PwnedPassword $pwnedPassword;

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    /** @test */
    public function successfulRangeLookupReturnsAPositiveInteger(): void
    {
        $list = self::mockPasswordList();
        $client = Mockery::mock(Client::class);
        $client->allows([
            'request' => new Response(200, [], $list),
        ]);

        $pwnedPassword = new PwnedPassword(new HibpHttp(null, $client));
        $count = $pwnedPassword->rangeFromHash('5BAA61E4C9B93F3F0682250B6CF8331B7EE68FD8');

        self::assertSame(200, $pwnedPassword->getStatusCode());
        self::assertIsInt($count);
        self::assertSame(3861493, $count);
    }

    /** @test */
    public function successfulRangeWithPaddingReturnsAPositiveInteger(): void
    {
        $client = Mockery::mock(Client::class);
        $client->allows([
            'request' => new Response(200, [], self::mockPasswordList()),
        ]);

        $pwnedPassword = new PwnedPassword(new HibpHttp(null, $client));
        $count = $pwnedPassword->paddedRangeFromHash('5BAA61E4C9B93F3F0682250B6CF8331B7EE68FD8');

        self::assertSame(200, $pwnedPassword->getStatusCode());
        self::assertIsInt($count);
        self::assertSame(3861493, $count);
    }

    /** @test */
    public function failedRangeLookupReturnsZero(): void
    {
        $client = Mockery::mock(Client::class);
        $client->allows([
            'request' => new Response(200, [], self::mockPasswordList()),
        ]);

        $pwnedPassword = new PwnedPassword(new HibpHttp(null, $client));
        $count = $pwnedPassword->rangeFromHash('0000000000000000000000000000000000000000');

        self::assertSame(200, $pwnedPassword->getStatusCode());
        self::assertEquals(0, $count);
    }

    /** @test */
    public function successfulRangeDataLookupReturnsAValidCollection(): void
    {
        $client = Mockery::mock(Client::class);
        $client->allows([
            'request' => new Response(200, [], self::mockPasswordList()),
        ]);

        $pwnedPassword = new PwnedPassword(new HibpHttp(null, $client));
        $response = $pwnedPassword->rangeDataFromHash('5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8');

        self::assertSame(200, $pwnedPassword->getStatusCode());
        self::assertGreaterThan(0, $response->last()['count']);
    }

    /** @test */
    public function successfulPaddedRangeDataLookupReturnsAValidCollectionWithZeroCountElements(): void
    {
        $client = Mockery::mock(Client::class);
        $client->allows([
            'send' => new Response(200, [], self::mockPasswordList()),
        ]);

        $pwnedPassword = new PwnedPassword(new HibpHttp(null, $client));
        $response = $pwnedPassword->paddedRangeDataFromHash('5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8');

        self::assertSame(200, $pwnedPassword->getStatusCode());
        self::assertSame(5, $response->last()['count']);
    }

    /** @test */
    public function strippedSuccessfulPaddedRangeReturnsAValidCollectionWithoutZeroCountElements(): void
    {
        $hash = '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8';

        $client = Mockery::mock(Client::class);
        $client->allows([
            'send' => new Response(200, [], self::mockPasswordList()),
        ]);

        $pwnedPassword = new PwnedPassword(new HibpHttp(null, $client));
        $response = $pwnedPassword->paddedRangeDataFromHash($hash);

        self::assertSame(200, $pwnedPassword->getStatusCode());
        self::assertGreaterThan(
            0,
            PwnedPassword::stripZeroMatchesData($response, $hash)->last()
        );
    }

    /** @test */
    public function invalidRangeThrowsARequestException(): void
    {
        $this->expectException(RequestException::class);

        $request = Mockery::mock(Request::class);

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('request')
            ->andThrow(new RequestException('', $request));

        $pwnedPassword = new PwnedPassword(new HibpHttp(null, $client));
        $pwnedPassword->rangeFromHash('&&');
    }

    /** @test */
    public function invalidRangeDataThrowsAnException(): void
    {
        $this->expectException(RequestException::class);

        $request = Mockery::mock(Request::class);

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('request')
            ->andThrow(new RequestException('', $request));

        $pwnedPassword = new PwnedPassword(new HibpHttp(null, $client));
        $pwnedPassword->rangeDataFromHash('&&');
    }

    /** @test */
    public function strippingPaddedRangeElementMatchingHashThrowsAPaddingHashCollisionException(): void
    {
        $this->expectException(PaddingHashCollisionException::class);

        $hash = '5BAA61E4C9B93F3F0682250B6CF8331B7EE68FD8';

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('request')
            ->andThrow(new PaddingHashCollisionException(''));

        $pwnedPassword = new PwnedPassword(new HibpHttp(null, $client));
        $response = $pwnedPassword->paddedRangeDataFromHash($hash);
    }

    private static function mockPasswordList(): string
    {
        $data = file_get_contents(sprintf('%s/_responses/passwords/password_results.txt', __DIR__));

        return (false !== $data) ? trim($data) : '';
    }
}
