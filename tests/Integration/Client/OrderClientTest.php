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
use SandwaveIo\FSecure\Entity\NewOrder;
use SandwaveIo\FSecure\FsecureClient;

final class OrderClientTest extends TestCase
{
    public function testGet(): void
    {
        $json = '{"items":[{"customerReference":"sdf|456","storeId":7112,"created":"2022-01-25T10:16:31.000Z","variations":[{"productId":21,"productLine":"","productTitle":"F-Secure TOTAL Recurring","sellType":"continuous","language":"en","type":"recurring","duration":0,"amount":15,"sku":"xx15","ean":"xx15","variationId":641,"licenseKey":"ABCDE-FGHIJ-KLMNO-PQRST","installationUrl":"https://localhost/ABCDE-FGHIJ-KLMNO-PQRST","distributorPrice":"9.95","resellerPrice":null,"suggestedRetailPrice":null}]},{"customerReference":"sdf|456","storeId":7112,"created":"2022-01-25T10:11:26.000Z","variations":[{"productId":21,"productLine":"","productTitle":"F-Secure TOTAL Recurring","sellType":"continuous","language":"en","type":"recurring","duration":0,"amount":15,"sku":"xx15","ean":"xx15","variationId":641,"licenseKey":"ABCDE-FGHIJ-KLMNO-PQRST","installationUrl":"https://localhost/ABCDE-FGHIJ-KLMNO-PQRST","distributorPrice":"9.95","resellerPrice":null,"suggestedRetailPrice":null}]},{"customerReference":"sdf|456","storeId":7112,"created":"2022-01-25T10:11:19.000Z","variations":[{"productId":21,"productLine":"","productTitle":"F-Secure TOTAL Recurring","sellType":"continuous","language":"en","type":"recurring","duration":0,"amount":10,"sku":"xx10","ean":"xx10","variationId":640,"licenseKey":"ABCDE-FGHIJ-KLMNO-PQRST","installationUrl":"https://localhost/ABCDE-FGHIJ-KLMNO-PQRST","distributorPrice":"6.62","resellerPrice":null,"suggestedRetailPrice":null}]},{"customerReference":"","storeId":7112,"created":"2021-12-07T14:05:16.000Z","variations":[{"productId":84,"productLine":"","productTitle":"TOTAL 1Y 5D","sellType":"continuous","language":"en","type":"new","duration":360,"amount":5,"sku":"sku","ean":"ean","variationId":529,"licenseKey":"ABCDE-FGHIJ-KLMNO-PQRST","installationUrl":"https://localhost/ABCDE-FGHIJ-KLMNO-PQRST","distributorPrice":null,"resellerPrice":null,"suggestedRetailPrice":null}]}]}';

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

        $restClient = new RestClient($guzzle, $serializer);

        $fsecureClient = new FsecureClient($restClient);
        $orderCollection = $fsecureClient->getOrderClient()->get();

        self::assertCount(4, $orderCollection->items);
        $firstOrder = $orderCollection->items[0];

        self::assertSame('sdf|456', $firstOrder->customerReference);
        self::assertSame(7112, $firstOrder->storeId);
        self::assertSame('2022-01-25T10:16:31.000Z', $firstOrder->created->format('Y-m-d\TH:i:s.v\Z'));

        self::assertCount(1, $firstOrder->variations);
        $orderVariation = $firstOrder->variations[0];
        self::assertSame(21, $orderVariation->productId);
        self::assertSame('', $orderVariation->productLine);
        self::assertSame('F-Secure TOTAL Recurring', $orderVariation->productTitle);
        self::assertSame('continuous', $orderVariation->sellType);
        self::assertSame('en', $orderVariation->language);
        self::assertSame('recurring', $orderVariation->type);
        self::assertSame(0, $orderVariation->duration);
        self::assertSame(15, $orderVariation->amount);
        self::assertSame('xx15', $orderVariation->sku);
        self::assertSame('xx15', $orderVariation->ean);
        self::assertSame(641, $orderVariation->variationId);
        self::assertSame('ABCDE-FGHIJ-KLMNO-PQRST', $orderVariation->licenseKey);
        self::assertSame('https://localhost/ABCDE-FGHIJ-KLMNO-PQRST', $orderVariation->installationUrl);
        self::assertSame('9.95', $orderVariation->distributorPrice);
        self::assertNull($orderVariation->resellerPrice);
        self::assertNull($orderVariation->suggestedRetailPrice);
    }

    public function testCreate(): void
    {
        $json = '{"customerReference":"Test order","storeId":123,"created":"2020-01-02T02:01:01.000Z","rows":[{"licenseKey":"ABCDE-FGHIJ-KLMNO-PQRST","installationUrl":"https://localhost/ABCDE-FGHIJ-KLMNO-PQRST","variationId":1,"type":"new","duration":360,"amount":1,"sku":"FCFXBR1N001XH","ean":"6430052573209","productId":10,"productTitle":"Safe","language":"en","productLine":"","sellType":"continuous","distributorPrice":123.45,"resellerPrice":123.45,"suggestedRetailPrice":123.45}]}';

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

        $restClient = new RestClient($guzzle, $serializer);

        $newOrder = new NewOrder(1, 'Test order', 123);

        $fsecureClient = new FsecureClient($restClient);
        $order = $fsecureClient->getOrderClient()->create($newOrder);

        self::assertSame($newOrder->customerReference, $order->customerReference);
        self::assertSame($newOrder->storeId, $order->storeId);
        self::assertSame('2020-01-02T02:01:01.000Z', $order->created->format('Y-m-d\TH:i:s.v\Z'));
        self::assertCount(1, $order->rows);

        $orderVariation = $order->rows[0];
        self::assertSame(10, $orderVariation->productId);
        self::assertSame('', $orderVariation->productLine);
        self::assertSame('Safe', $orderVariation->productTitle);
        self::assertSame('continuous', $orderVariation->sellType);
        self::assertSame('en', $orderVariation->language);
        self::assertSame('new', $orderVariation->type);
        self::assertSame(360, $orderVariation->duration);
        self::assertSame(1, $orderVariation->amount);
        self::assertSame('FCFXBR1N001XH', $orderVariation->sku);
        self::assertSame('6430052573209', $orderVariation->ean);
        self::assertSame(1, $orderVariation->variationId);
        self::assertSame('ABCDE-FGHIJ-KLMNO-PQRST', $orderVariation->licenseKey);
        self::assertSame('https://localhost/ABCDE-FGHIJ-KLMNO-PQRST', $orderVariation->installationUrl);
        self::assertSame('123.45', $orderVariation->distributorPrice);
        self::assertSame('123.45', $orderVariation->resellerPrice);
        self::assertSame('123.45', $orderVariation->suggestedRetailPrice);
    }
}
