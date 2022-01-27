<?php

declare(strict_types=1);

namespace SandwaveIo\Acronis\Tests\Unit\Client;

use GuzzleHttp\ClientInterface;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;
use SandwaveIo\FSecure\Client\RestClient;
use SandwaveIo\FSecure\Exception\DeserializationException;
use SandwaveIo\FSecure\Service\ExceptionConvertor;

final class RestClientTest extends TestCase
{
    public function testInvalidReturnType(): void
    {
        $client = new RestClient(
            $this->createMock(ClientInterface::class),
            $this->createMock(SerializerInterface::class),
            new ExceptionConvertor()
        );

        /** @var class-string $class */
        $class = 'SandwaveIo\Acronis\NotExistingClass';

        $this->expectException(DeserializationException::class);
        $client->getEntity('url', $class);
    }
}
