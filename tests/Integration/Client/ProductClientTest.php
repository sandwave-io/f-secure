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
        $jsonResponse = '{"items":[{"type":"recurring","duration":0,"amount":10,"sku":"xx10","ean":"xx10","distributorPrice":"6.62","resellerPrice":null,"suggestedRetailPrice":null,"variationId":640,"sellType":"continuous","canSell":true,"productId":21,"productLine":"","productTitle":"F-Secure TOTAL Recurring","language":"en"},{"type":"recurring","duration":0,"amount":15,"sku":"xx15","ean":"xx15","distributorPrice":"9.95","resellerPrice":null,"suggestedRetailPrice":null,"variationId":641,"sellType":"continuous","canSell":true,"productId":21,"productLine":"","productTitle":"F-Secure TOTAL Recurring","language":"en"},{"type":"recurring","duration":0,"amount":1,"sku":"xx01","ean":"xx01","distributorPrice":"0.67","resellerPrice":null,"suggestedRetailPrice":null,"variationId":642,"sellType":"continuous","canSell":true,"productId":21,"productLine":"","productTitle":"F-Secure TOTAL Recurring","language":"en"},{"type":"recurring","duration":0,"amount":20,"sku":"xx20","ean":"xx20","distributorPrice":"13.27","resellerPrice":null,"suggestedRetailPrice":null,"variationId":643,"sellType":"continuous","canSell":true,"productId":21,"productLine":"","productTitle":"F-Secure TOTAL Recurring","language":"en"},{"type":"recurring","duration":0,"amount":25,"sku":"xx25","ean":"xx20","distributorPrice":"16.58","resellerPrice":null,"suggestedRetailPrice":null,"variationId":644,"sellType":"continuous","canSell":true,"productId":21,"productLine":"","productTitle":"F-Secure TOTAL Recurring","language":"en"},{"type":"recurring","duration":0,"amount":3,"sku":"xx03","ean":"xx03","distributorPrice":"1.99","resellerPrice":null,"suggestedRetailPrice":null,"variationId":645,"sellType":"continuous","canSell":true,"productId":21,"productLine":"","productTitle":"F-Secure TOTAL Recurring","language":"en"},{"type":"recurring","duration":0,"amount":5,"sku":"xx05","ean":"xx05","distributorPrice":"3.32","resellerPrice":null,"suggestedRetailPrice":null,"variationId":646,"sellType":"continuous","canSell":true,"productId":21,"productLine":"","productTitle":"F-Secure TOTAL Recurring","language":"en"},{"type":"recurring","duration":0,"amount":7,"sku":"xx07","ean":"xx07","distributorPrice":"4.64","resellerPrice":null,"suggestedRetailPrice":null,"variationId":647,"sellType":"continuous","canSell":true,"productId":21,"productLine":"","productTitle":"F-Secure TOTAL Recurring","language":"en"}]}';

        $mockHandler = new MockHandler(
            [new Response(200, [], $jsonResponse)]
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
