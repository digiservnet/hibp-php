<?php

namespace Icawebdesign\Hibp\Tests;

use GuzzleHttp\Exception\RequestException;
use Icawebdesign\Hibp\Exception\PasteNotFoundException;
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

        $this->paste = new Paste($this->apiKey);
    }

    protected function delay(int $microseconds = 1600000): void
    {
        usleep($microseconds);
    }

    /** @test */
    public function successfulLookupReturnsACollection(): void
    {
        $this->delay();
        $pastes = $this->paste->lookup('test@example.com');

        self::assertSame(200, $this->paste->getStatusCode());
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
        $this->delay();
        $this->expectException(RequestException::class);

        $this->paste->lookup('invalid_email_address');
    }

    /** @test */
    public function notFoundLookupThrowsPasteNotFoundException(): void
    {
        $this->delay();
        $this->expectException(PasteNotFoundException::class);

        $this->paste->lookup('unknown-address@example.com');
    }
}
