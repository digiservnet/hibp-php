<?php

namespace Icawebdesign\Hibp\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Icawebdesign\Hibp\Exception\PasteNotFoundException;
use Icawebdesign\Hibp\HibpHttp;
use Icawebdesign\Hibp\Paste\Paste;
use Icawebdesign\Hibp\Paste\PasteEntity;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

use function file_get_contents;
use function sprintf;

class PasteTest extends TestCase
{
    protected string $apiKey = '';

    protected Paste $paste;

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    #[Test]
    public function successful_lookup_returns_a_collection(): void
    {
        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andReturn(new Response(HttpResponse::HTTP_OK, [], self::mockAllPastes()));

        $paste = new Paste(new HibpHttp(client: $client));
        $pastes = $paste->lookup(emailAddress: 'test@example.com');

        self::assertSame(HttpResponse::HTTP_OK, $paste->statusCode);
        self::assertCount(3, $pastes);

        /** @var PasteEntity $account */
        $account = $pastes->first();

        self::assertSame('Pastebin', $account->source);
        self::assertSame('8Q0BvKD8', $account->id);
        self::assertSame(139, $account->emailCount);
        self::assertSame("https://pastebin.com/{$account->id}", $account->link);
    }

    #[Test]
    public function invalid_lookup_request_throws_a_request_exception(): void
    {
        $this->expectException(RequestException::class);

        $mockedResponse = Mockery::mock(Response::class);
        $mockedResponse
            ->expects('getStatusCode')
            ->once()
            ->andReturn(HttpResponse::HTTP_BAD_REQUEST);

        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andThrow(
                new ClientException(
                    message: '',
                    request: Mockery::mock(Request::class),
                    response: $mockedResponse,
                ),
            );

        $paste = new Paste(new HibpHttp(client: $client));
        $paste->lookup(emailAddress: 'invalid_email_address');
    }

    #[Test]
    public function invalid_lookup_throws_a_client_exception(): void
    {
        $this->expectException(ClientException::class);

        $mockedResponse = Mockery::mock(Response::class);
        $mockedResponse
            ->expects('getStatusCode')
            ->once()
            ->andReturn(0);

        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andThrow(
                new ClientException(
                    message: '',
                    request: Mockery::mock(Request::class),
                    response: $mockedResponse,
                ),
            );

        $paste = new Paste(new HibpHttp(client: $client));
        $paste->lookup(emailAddress: 'invalid_email_address');
    }

    #[Test]
    public function unknown_lookup_throws_a_request_exception(): void
    {
        $this->expectException(PasteNotFoundException::class);

        $mockedResponse = Mockery::mock(Response::class);
        $mockedResponse
            ->expects('getStatusCode')
            ->once()
            ->andReturn(HttpResponse::HTTP_NOT_FOUND);

        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andThrow(
                new ClientException(
                    message: '',
                    request: Mockery::mock(Request::class),
                    response: $mockedResponse,
                ),
            );

        $paste = new Paste(new HibpHttp(client: $client));
        $paste->lookup(emailAddress: 'invalid_email_address');
    }

    #[Test]
    public function not_found_lookup_throws_paste_not_found_exception(): void
    {
        $this->expectException(PasteNotFoundException::class);

        $client = Mockery::mock(Client::class);
        $client
            ->expects('request')
            ->once()
            ->andThrow(
                new PasteNotFoundException(
                    message: 'message',
                ),
            );

        $paste = new Paste(new HibpHttp(client: $client));
        $paste->lookup(emailAddress: 'unknown-address@example.com');
    }

    private static function mockAllPastes(): string
    {
        $data = file_get_contents(sprintf('%s/_responses/pastes/all_pastes.json', __DIR__));

        return (false !== $data) ? $data : '[]';
    }
}
