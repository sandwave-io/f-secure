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
use SandwaveIo\FSecure\Client\OrderClient;
use SandwaveIo\FSecure\Entity\NewOrder;
use SandwaveIo\FSecure\Entity\SuspendOrder;
use SandwaveIo\FSecure\Exception\BadRequestException;
use SandwaveIo\FSecure\Exception\DeserializationException;
use SandwaveIo\FSecure\Exception\FSecureException;
use SandwaveIo\FSecure\Service\ThrowableConvertor;

final class OrderClientTest extends TestCase
{
    private ThrowableConvertor $exceptionConvertor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->exceptionConvertor = new ThrowableConvertor();
    }

    public function testGet(): void
    {
        $json = (string) file_get_contents(__DIR__ . '/../Data/Response/GetOrders.json');

        $mockHandler = new MockHandler(
            [new Response(200, [], $json)]
        );
        $stack = HandlerStack::create($mockHandler);
        $httpClient = new GuzzleClient(['handler' => $stack]);

        $serializerBuilder = new SerializerBuilder();
        $serializer = $serializerBuilder->setPropertyNamingStrategy(
            new SerializedNameAnnotationStrategy(
                new IdenticalPropertyNamingStrategy()
            )
        )->build();

        $client = new Client($httpClient, $serializer, $this->exceptionConvertor);

        $orderClient = new OrderClient($client);
        $orderCollection = $orderClient->get();

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

    public function testCreateInvalidVariationId(): void
    {
        $this->expectException(FSecureException::class);
        $json = (string) file_get_contents(__DIR__ . '/../Data/Response/NewOrderInvalidVariationId.json');

        $mockHandler = new MockHandler(
            [new Response(400, [], $json)]
        );
        $stack = HandlerStack::create($mockHandler);
        $httpClient = new GuzzleClient(['handler' => $stack]);

        $serializerBuilder = new SerializerBuilder();
        $serializer = $serializerBuilder->setPropertyNamingStrategy(
            new SerializedNameAnnotationStrategy(
                new IdenticalPropertyNamingStrategy()
            )
        )->build();

        $client = new Client($httpClient, $serializer, $this->exceptionConvertor);

        $newOrder = new NewOrder(1111111111, 'Test order', 123);

        $orderClient = new OrderClient($client);
        $orderClient->create($newOrder);
    }

    public function testCreate(): void
    {
        $json = (string) file_get_contents(__DIR__ . '/../Data/Response/NewOrder.json');

        $mockHandler = new MockHandler(
            [new Response(200, [], $json)]
        );
        $stack = HandlerStack::create($mockHandler);
        $httpClient = new GuzzleClient(['handler' => $stack]);

        $serializerBuilder = new SerializerBuilder();
        $serializer = $serializerBuilder->setPropertyNamingStrategy(
            new SerializedNameAnnotationStrategy(
                new IdenticalPropertyNamingStrategy()
            )
        )->build();

        $client = new Client($httpClient, $serializer, $this->exceptionConvertor);

        $newOrder = new NewOrder(1, 'Test order', 123);

        $orderClient = new OrderClient($client);
        $order = $orderClient->create($newOrder);

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

    public function testSuspendOrderBadRequest(): void
    {
        $this->expectException(BadRequestException::class);
        $jsonResponse = (string) file_get_contents(__DIR__ . '/../Data/Response/SuspendOrderMissingRequired.json');

        $mockHandler = new MockHandler(
            [new Response(400, [], $jsonResponse)]
        );
        $stack = HandlerStack::create($mockHandler);
        $httpClient = new GuzzleClient(['handler' => $stack]);

        $serializerBuilder = new SerializerBuilder();
        $serializer = $serializerBuilder->setPropertyNamingStrategy(
            new SerializedNameAnnotationStrategy(
                new IdenticalPropertyNamingStrategy()
            )
        )->build();

        $client = new Client($httpClient, $serializer, $this->exceptionConvertor);

        $orderClient = new OrderClient($client);
        $orderClient->suspend(new SuspendOrder());
    }

    public function testSuspendOrderByLicenseKey(): void
    {
        $jsonResponse = (string) file_get_contents(__DIR__ . '/../Data/Response/SuspendOrderByLicenseKey.json');

        $mockHandler = new MockHandler(
            [new Response(200, [], $jsonResponse)]
        );
        $stack = HandlerStack::create($mockHandler);
        $httpClient = new GuzzleClient(['handler' => $stack]);

        $serializerBuilder = new SerializerBuilder();
        $serializer = $serializerBuilder->setPropertyNamingStrategy(
            new SerializedNameAnnotationStrategy(
                new IdenticalPropertyNamingStrategy()
            )
        )->build();

        $client = new Client($httpClient, $serializer, $this->exceptionConvertor);
        $suspendOrder = new SuspendOrder();
        $suspendOrder->licenseKey = 'XXXX-YYYY-ZZZZ-AAAA';

        $orderClient = new OrderClient($client);
        $result = $orderClient->suspend($suspendOrder);

        self::assertCount(1, $result->items);
        self::assertSame($suspendOrder->licenseKey, $result->items[0]->licenseKey);
        self::assertSame('customer1', $result->items[0]->customerReference);
    }

    public function testSuspendOrderByCustomerReference(): void
    {
        $jsonResponse = (string) file_get_contents(__DIR__ . '/../Data/Response/SuspendOrderByCustomerReference.json');

        $mockHandler = new MockHandler(
            [new Response(200, [], $jsonResponse)]
        );
        $stack = HandlerStack::create($mockHandler);
        $httpClient = new GuzzleClient(['handler' => $stack]);

        $serializerBuilder = new SerializerBuilder();
        $serializer = $serializerBuilder->setPropertyNamingStrategy(
            new SerializedNameAnnotationStrategy(
                new IdenticalPropertyNamingStrategy()
            )
        )->build();

        $client = new Client($httpClient, $serializer, $this->exceptionConvertor);

        $suspendOrder = new SuspendOrder();
        $suspendOrder->customerReference = 'customer1';

        $orderClient = new OrderClient($client);
        $result = $orderClient->suspend($suspendOrder);

        self::assertCount(2, $result->items);
        self::assertCount(1, $result->alreadySuspendedItems);

        self::assertSame($suspendOrder->customerReference, $result->items[0]->customerReference);
        self::assertSame('XXXX-YYYY-ZZZZ-BBBB', $result->items[0]->licenseKey);

        self::assertSame($suspendOrder->customerReference, $result->items[1]->customerReference);
        self::assertSame('XXXX-YYYY-ZZZZ-CCCC', $result->items[1]->licenseKey);

        self::assertSame($suspendOrder->customerReference, $result->alreadySuspendedItems[0]->customerReference);
        self::assertSame('XXXX-YYYY-ZZZZ-AAAA', $result->alreadySuspendedItems[0]->licenseKey);
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
        $httpClient = new GuzzleClient(['handler' => $stack]);

        $client = new Client($httpClient, $serializeMock, new ThrowableConvertor());

        $productClient = new OrderClient($client);
        $productClient->create(new NewOrder(1, 'fake', 1));
    }
}
