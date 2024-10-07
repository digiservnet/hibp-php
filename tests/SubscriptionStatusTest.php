<?php

namespace Icawebdesign\Hibp\Tests;

use Mockery;
use Carbon\Carbon;
use RuntimeException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Icawebdesign\Hibp\HibpHttp;
use PHPUnit\Framework\Attributes\Test;
use GuzzleHttp\Exception\ClientException;
use Icawebdesign\Hibp\Enums\SubscriptionTier;
use Icawebdesign\Hibp\Subscription\Subscription;
use Icawebdesign\Hibp\Exception\UnauthorizedException;
use Icawebdesign\Hibp\Subscription\SubscriptionStatusEntity;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class SubscriptionStatusTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    #[Test]
    public function a_valid_api_key_will_return_status_of_the_subscription(): void
    {
        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andReturn(new Response(HttpResponse::HTTP_OK, [], self::mockSubscriptionStatus()));

        $status = (new Subscription(hibpHttp: new HibpHttp(client: $client)))->status();

        self::assertEquals(new Carbon('2024-11-07T21:26:19'), $status->expires);
        self::assertSame(SubscriptionTier::Pwned1, $status->tier);
        self::assertNotEmpty($status->description);
        self::assertSame(25, $status->domainSearchMaxBreachedAccounts);
        self::assertSame(10, $status->requestsPerMinute);
    }

    #[Test]
    public function invalid_subscription_data_throws_runtime_exception(): void
    {
        $this->expectException(RuntimeException::class);

        new SubscriptionStatusEntity(data: null);
    }

    private static function mockSubscriptionStatus(): string
    {
        $data = file_get_contents(
            sprintf('%s/_responses/subscription/status.json', __DIR__),
        );

        return ($data !== false) ? $data : '[]';
    }
}
