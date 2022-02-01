<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure\Tests\Integration\Client;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;
use SandwaveIo\FSecure\Client\Client;
use SandwaveIo\FSecure\Client\ProductClient;
use SandwaveIo\FSecure\Exception\DeserializationException;
use SandwaveIo\FSecure\Service\ThrowableConvertor;

final class ProductClientTest extends TestCase
{
    public function testGet(): void
    {
        $json = (string) file_get_contents(__DIR__ . '/../Data/Response/GetAvailableProducts.json');

        $mockHandler = new MockHandler(
            [new Response(200, [], $json)]
        );
        $stack = HandlerStack::create($mockHandler);
        $guzzleClient = new GuzzleClient(['handler' => $stack]);

        $serializerBuilder = new SerializerBuilder();
        $serializer = $serializerBuilder->setPropertyNamingStrategy(
            new SerializedNameAnnotationStrategy(
                new IdenticalPropertyNamingStrategy()
            )
        )->build();

        $client = new Client($guzzleClient, $serializer, new ThrowableConvertor());

        $productClient = new ProductClient($client);
        $productCollection = $productClient->get();

        $firstProduct = $productCollection->items[0];

        self::assertCount(8, $productCollection->items);
        self::assertSame('recurring', $firstProduct->type);
        self::assertSame(0, $firstProduct->duration);
        self::assertSame(10, $firstProduct->amount);
        self::assertSame('xx10', $firstProduct->sku);
        self::assertSame('xx10', $firstProduct->ean);
        self::assertSame('6.62', $firstProduct->distributorPrice);
        self::assertNull($firstProduct->resellerPrice);
        self::assertNull($firstProduct->suggestedRetailPrice);
        self::assertSame(640, $firstProduct->variationId);
        self::assertSame('continuous', $firstProduct->sellType);
        self::assertTrue($firstProduct->canSell);
        self::assertSame(21, $firstProduct->productId);
        self::assertSame('', $firstProduct->productLine);
        self::assertSame('F-Secure TOTAL Recurring', $firstProduct->productTitle);
        self::assertSame('en', $firstProduct->language);
    }

    public function testGetDeserializeException(): void
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
        $guzzleClient = new GuzzleClient(['handler' => $stack]);

        $client = new Client($guzzleClient, $serializeMock, new ThrowableConvertor());

        $productClient = new ProductClient($client);
        $productClient->get();
    }
}
