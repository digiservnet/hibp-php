<?php

namespace Icawebdesign\Hibp\Tests;

use Mockery;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Icawebdesign\Hibp\HibpHttp;
use PHPUnit\Framework\Attributes\Test;
use Icawebdesign\Hibp\StealerLog\StealerLog;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

use function sprintf;
use function file_get_contents;

class StealerLogTest extends TestCase
{
    #[Test]
    public function getting_stealer_logs_by_email_returns_a_collection(): void
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
        $stealerLogs = $stealerLog->getStealerLogsByEmail(emailAddress: 'test@example');

        self::assertSame(HttpResponse::HTTP_OK, $stealerLog->statusCode);
        self::assertCount(2, $stealerLogs);
        self::assertSame('netflix.com', $stealerLogs[0]);
        self::assertSame('spotify.com', $stealerLogs[1]);
    }

    #[Test]
    public function getting_stealer_logs_by_email_with_no_results_returns_an_empty_collection(): void
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
        $stealerLogs = $stealerLog->getStealerLogsByEmail(emailAddress: 'test@example');

        self::assertSame(HttpResponse::HTTP_NOT_FOUND, $stealerLog->statusCode);
        self::assertCount(0, $stealerLogs);
    }

    private static function mockStealerLogsByEmailAddress(): string
    {
        $data = file_get_contents(sprintf('%s/_responses/stealerlogs/by_email.json', __DIR__));

        return (false !== $data) ? $data : '[]';
    }
}
