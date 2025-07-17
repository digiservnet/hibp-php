<?php

namespace Icawebdesign\Hibp\Tests;

use GuzzleHttp\Client;
use Icawebdesign\Hibp\HibpHttp;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class HibpHttpTest extends TestCase
{
    #[Test]
    public function can_create_a_client(): void
    {
        $client = new HibpHttp();

        self::assertInstanceOf(HibpHttp::class, $client);
    }

    #[Test]
    public function can_create_a_client_with_api_key(): void
    {
        $client = new HibpHttp('THE_API_KEY');

        self::assertInstanceOf(HibpHttp::class, $client);
    }

    #[Test]
    public function can_use_instance_of_an_http_client_to_create_a_client(): void
    {
        $httpClient = Mockery::mock(Client::class);
        $client = new HibpHttp('THE_API_KEY', $httpClient);

        self::assertInstanceOf(HibpHttp::class, $client);
    }
}
