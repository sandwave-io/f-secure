<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure\Tests\Unit\Client;

use GuzzleHttp\ClientInterface;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;
use SandwaveIo\FSecure\Client\RestClient;
use SandwaveIo\FSecure\Exception\DeserializationException;
use SandwaveIo\FSecure\Service\ThrowableConvertor;

final class RestClientTest extends TestCase
{
    public function testInvalidReturnType(): void
    {
        $client = new RestClient(
            $this->createMock(ClientInterface::class),
            $this->createMock(SerializerInterface::class),
            new ThrowableConvertor()
        );

        /** @var class-string $class */
        $class = 'SandwaveIo\FSecure\NotExistingClass';

        $this->expectException(DeserializationException::class);
        $client->getEntity('url', $class);
    }
}
