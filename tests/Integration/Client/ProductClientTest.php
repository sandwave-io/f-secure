<?php

declare(strict_types=1);

namespace SandwaveIo\Acronis\Tests\Integration\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;
use SandwaveIo\FSecure\Client\RestClient;
use SandwaveIo\FSecure\FsecureClient;
use SandwaveIo\FSecure\Service\ExceptionConvertor;

final class ProductClientTest extends TestCase
{
    public function testGet(): void
    {
        $json = (string) file_get_contents(__DIR__ . '/../Data/Response/GetAvailableProducts.json');

        $mockHandler = new MockHandler(
            [new Response(200, [], $json)]
        );
        $stack = HandlerStack::create($mockHandler);
        $guzzle = new Client(['handler' => $stack]);

        $serializerBuilder = new SerializerBuilder();
        $serializer = $serializerBuilder->setPropertyNamingStrategy(
            new SerializedNameAnnotationStrategy(
                new IdenticalPropertyNamingStrategy()
            )
        )->build();

        $restClient = new RestClient($guzzle, $serializer, new ExceptionConvertor());

        $fsecureClient = new FsecureClient($restClient);
        $productCollection = $fsecureClient->getProductClient()->get();
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
}
