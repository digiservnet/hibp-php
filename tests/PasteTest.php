<?php

namespace Icawebdesign\Hibp\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Icawebdesign\Hibp\Exception\PasteNotFoundException;
use Icawebdesign\Hibp\HibpHttp;
use Icawebdesign\Hibp\Paste\Paste;
use Icawebdesign\Hibp\Paste\PasteEntity;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * Paste tests
 *
 * @author Ian <ian@ianh.io>
 * @since 05/03/2018
 */

class PasteTest extends TestCase
{
    /** @var string */
    protected string $apiKey = '';

    /** @var Paste */
    protected Paste $paste;

    public function setUp(): void
    {
        parent::setUp();
        $apiKey = file_get_contents(sprintf('%s/../api.key', __DIR__));

        if (false !== $apiKey) {
            $this->apiKey = $apiKey;
        }
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    /** @test */
    public function successfulLookupReturnsACollection(): void
    {
        $client = Mockery::mock(Client::class);
        $client->allows([
            'request' => new Response(200, [], self::mockAllPastes()),
        ]);

        $paste = new Paste(new HibpHttp(null, $client));
        $pastes = $paste->lookup('test@example.com');

        self::assertSame(200, $paste->getStatusCode());
        self::assertGreaterThan(0, $pastes->count());

        /** @var PasteEntity $account */
        $account = $pastes->first();

        self::assertNotEmpty($account->getSource());
        self::assertNotEmpty($account->getId());
        self::assertIsInt($account->getEmailCount());
        self::assertIsString($account->getLink());
        self::assertIsInt($account->getEmailCount());
        self::assertGreaterThan(0, $account->getEmailCount());
    }

    /** @test */
    public function invalidLookupThrowsException(): void
    {
        $this->expectException(RequestException::class);

        $request = Mockery::mock(Request::class);

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('request')
            ->andThrow(new RequestException('', $request));

        $paste = new Paste(new HibpHttp(null, $client));
        $paste->lookup('invalid_email_address');
    }

    /** @test */
    public function notFoundLookupThrowsPasteNotFoundException(): void
    {
        $this->expectException(PasteNotFoundException::class);

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('request')
            ->andThrow(new PasteNotFoundException(''));

        $paste = new Paste(new HibpHttp(null, $client));
        $paste->lookup('unknown-address@example.com');
    }

    private static function mockAllPastes(): string
    {
        $data = file_get_contents(sprintf('%s/_responses/pastes/all_pastes.json', __DIR__));

        return (false !== $data) ? $data : '[]';
    }
}
