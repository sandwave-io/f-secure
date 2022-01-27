<?php

declare(strict_types=1);

namespace SandwaveIo\Acronis\Tests\Integration\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;
use SandwaveIo\FSecure\Client\AuthClient;
use SandwaveIo\FSecure\Exception\FsecureException;
use SandwaveIo\FSecure\Service\ExceptionConvertor;

final class AuthClientTest extends TestCase
{
    private ExceptionConvertor $exceptionConvertor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->exceptionConvertor = new ExceptionConvertor();
    }

    /**
     * @throws GuzzleException
     */
    public function testGetToken(): void
    {
        $json = (string) file_get_contents(__DIR__ . '/../Data/Response/GetToken.json');

        $mockHandler = new MockHandler(
            [new Response(200, [], $json)]
        );
        $stack = HandlerStack::create($mockHandler);
        $guzzle = new Client(['handler' => $stack]);

        $serializerBuilder = new SerializerBuilder();
        $serializer = $serializerBuilder->build();

        $clientId = 'clientId';
        $clientSecret = 'clientSecret';

        $restClient = new AuthClient($clientId, $clientSecret, $guzzle, $serializer, $this->exceptionConvertor);

        $token = $restClient->getToken();

        self::assertSame('60048a266cc74468b9bdb574f12d4555cbefb3db', $token->accessToken);
        self::assertSame('Bearer', $token->tokenType);
        self::assertSame(3599, $token->expiresIn);
        self::assertSame('license_orders_write license_orders_read license_products_read', $token->scope);
    }

    public function testGetTokenInvalidScope(): void
    {
        $this->expectException(FsecureException::class);

        $json = (string) file_get_contents(__DIR__ . '/../Data/Response/GetTokenInvalidScope.json');

        $mockHandler = new MockHandler(
            [new Response(400, [], $json)]
        );
        $stack = HandlerStack::create($mockHandler);
        $guzzle = new Client(['handler' => $stack]);

        $serializerBuilder = new SerializerBuilder();
        $serializer = $serializerBuilder->build();

        $clientId = 'clientId';
        $clientSecret = 'clientSecret';

        $restClient = new AuthClient($clientId, $clientSecret, $guzzle, $serializer, $this->exceptionConvertor);

        $restClient->getToken();
    }
}
