<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure\Tests\Integration\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;
use SandwaveIo\FSecure\Client\AuthClient;
use SandwaveIo\FSecure\Exception\DeserializationException;
use SandwaveIo\FSecure\Exception\FsecureException;
use SandwaveIo\FSecure\Service\ThrowableConvertor;

final class AuthClientTest extends TestCase
{
    private ThrowableConvertor $exceptionConvertor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->exceptionConvertor = new ThrowableConvertor();
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
        $guzzleClient = new Client(['handler' => $stack]);

        $serializerBuilder = new SerializerBuilder();
        $serializer = $serializerBuilder->build();

        $clientId = 'clientId';
        $clientSecret = 'clientSecret';

        $client = new AuthClient($clientId, $clientSecret, $guzzleClient, $serializer, $this->exceptionConvertor);

        $token = $client->getToken();

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
        $guzzleClient = new Client(['handler' => $stack]);

        $serializerBuilder = new SerializerBuilder();
        $serializer = $serializerBuilder->build();

        $clientId = 'clientId';
        $clientSecret = 'clientSecret';

        $client = new AuthClient($clientId, $clientSecret, $guzzleClient, $serializer, $this->exceptionConvertor);

        $client->getToken();
    }

    public function testGetTokenDeserializeException(): void
    {
        $this->expectException(DeserializationException::class);
        $serializeMock = $this->createMock(SerializerInterface::class);
        $serializeMock->expects(self::once())
            ->method('deserialize')
            ->willReturn([]);

        $mockHandler = new MockHandler(
            [new Response(200, [], '{}')]
        );
        $stack = HandlerStack::create($mockHandler);
        $guzzleClient = new Client(['handler' => $stack]);

        $client = new AuthClient('clientId', 'clientSecret', $guzzleClient, $serializeMock, $this->exceptionConvertor);

        $client->getToken();
    }
}
