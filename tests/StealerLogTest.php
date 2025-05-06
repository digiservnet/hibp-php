<?php

namespace Icawebdesign\Hibp\Tests;

use Mockery;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Icawebdesign\Hibp\HibpHttp;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\Test;
use Icawebdesign\Hibp\StealerLog\StealerLog;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

use function sprintf;
use function json_decode;
use function file_get_contents;

use function PHPUnit\Framework\assertCount;

use const JSON_THROW_ON_ERROR;

class StealerLogTest extends TestCase
{
    #[Test]
    public function getting_stealer_logs_by_email_address_returns_a_collection(): void
    {
        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andReturn(
                new Response(
                    status: HttpResponse::HTTP_OK,
                    headers: [],
                    body: self::mockStealerLogsByEmailAddress(),
                ),
            );

        $stealerLog = new StealerLog(new HibpHttp(client: $client));
        $stealerLogs = $stealerLog->getStealerLogsByEmailAddress(emailAddress: 'test@example');

        self::assertSame(HttpResponse::HTTP_OK, $stealerLog->statusCode);
        self::assertCount(2, $stealerLogs);
        self::assertSame('netflix.com', $stealerLogs[0]);
        self::assertSame('spotify.com', $stealerLogs[1]);
    }

    #[Test]
    public function getting_stealer_logs_by_email_address_with_no_results_returns_an_empty_collection(): void
    {
        $client = Mockery::mock(Client::class);

        $client
            ->expects('request')
            ->once()
            ->andReturn(
                new Response(
                    status: HttpResponse::HTTP_NOT_FOUND,
                    headers: [],
                    body: '[]',
                ),
            );

        $stealerLog = new StealerLog(new HibpHttp(client: $client));
        $stealerLogs = $stealerLog->getStealerLogsByEmailAddress(emailAddress: 'test@example');

        self::assertSame(HttpResponse::HTTP_NOT_FOUND, $stealerLog->statusCode);
        self::assertCount(0, $stealerLogs);
    }

    #[Test]
    public function getting_stealer_logs_by_website_domain_returns_a_collection_of_email_addresses(): void
    {
        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andReturn(
                new Response(
                    status: HttpResponse::HTTP_OK,
                    headers: [],
                    body: self::mockStealerLogsByWebsiteDomain(),
                ),
            );

        $stealerLog = new StealerLog(new HibpHttp(client: $client));
        $stealerLogs = $stealerLog->getStealerLogsByWebsiteDomain(domain: 'example.com');

        self::assertSame(HttpResponse::HTTP_OK, $stealerLog->statusCode);
        self::assertCount(2, $stealerLogs);
        self::assertSame('andy@gmail.com', $stealerLogs[0]);
        self::assertSame('jane@gmail.com', $stealerLogs[1]);
    }

    #[Test]
    public function getting_stealer_logs_by_website_domain_with_no_results_returns_an_empty_collection(): void
    {
        $client = Mockery::mock(Client::class);

        $client
            ->expects('request')
            ->once()
            ->andReturn(
                new Response(
                    status: HttpResponse::HTTP_NOT_FOUND,
                    headers: [],
                    body: '[]',
                ),
            );

        $stealerLog = new StealerLog(new HibpHttp(client: $client));
        $stealerLogs = $stealerLog->getStealerLogsByWebsiteDomain(domain: 'invalid-domain.teeeldee');

        self::assertSame(HttpResponse::HTTP_NOT_FOUND, $stealerLog->statusCode);
        self::assertCount(0, $stealerLogs);
    }

    #[Test]
    public function getting_stealer_logs_by_email_domain_returns_a_collection_of_aliases(): void
    {
        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andReturn(
                new Response(
                    status: HttpResponse::HTTP_OK,
                    headers: [],
                    body: self::mockStealerLogsByEmailDomain(),
                ),
            );

        $data = Collection::make(
            json_decode(self::mockStealerLogsByEmailDomain(), associative: true, flags: JSON_THROW_ON_ERROR)
        );

        $stealerLog = new StealerLog(new HibpHttp(client: $client));
        $stealerLogs = $stealerLog->getStealerLogsByEmailDomain(domain: 'example.com');

        self::assertSame(HttpResponse::HTTP_OK, $stealerLog->statusCode);
        self::assertCount(2, $stealerLogs);
        self::assertEquals($data, $stealerLogs);
    }

    #[Test]
    public function getting_stealer_logs_by_email_domain_with_no_results_returns_an_empty_collection(): void
    {
        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andReturn(
                new Response(
                    status: HttpResponse::HTTP_NOT_FOUND,
                    headers: [],
                    body: '[]',
                ),
            );

        $stealerLog = new StealerLog(new HibpHttp(client: $client));
        $stealerLogs = $stealerLog->getStealerLogsByEmailDomain(domain: 'example.com');

        self::assertSame(HttpResponse::HTTP_NOT_FOUND, $stealerLog->statusCode);
        self::assertCount(0, $stealerLogs);
    }

    private static function mockStealerLogsByEmailAddress(): string
    {
        $data = file_get_contents(sprintf('%s/_responses/stealerlogs/by_email.json', __DIR__));

        return (false !== $data) ? $data : '[]';
    }

    private static function mockStealerLogsByWebsiteDomain(): string
    {
        $data = file_get_contents(
            sprintf('%s/_responses/stealerlogs/by_website_domain.json', __DIR__)
        );

        return (false !== $data) ? $data : '[]';
    }

    private static function mockStealerLogsByEmailDomain(): string
    {
        $data = file_get_contents(
            sprintf('%s/_responses/stealerlogs/by_email_domain.json', __DIR__)
        );

        return (false !== $data) ? $data : '[]';
    }
}
