<?php

namespace Icawebdesign\Hibp\Tests;

use Mockery;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Icawebdesign\Hibp\HibpHttp;
use PHPUnit\Framework\Attributes\Test;
use Icawebdesign\Hibp\Enums\SubscriptionTier;
use Icawebdesign\Hibp\Subscription\Subscription;
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
            ->andReturn(new Response(HttpResponse::HTTP_OK, [], json_encode([
                'SubscriptionName' => 'Pwned 1',
            ])));

        $status = (new Subscription(hibpHttp: new HibpHttp($client)))->status();

        self::assertSame(SubscriptionTier::Pwned1, $status->tier);
    }
}
