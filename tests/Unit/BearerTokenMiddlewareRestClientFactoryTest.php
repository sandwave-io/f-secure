<?php

declare(strict_types=1);

namespace SandwaveIo\Acronis\Tests\Unit;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use SandwaveIo\FSecure\BearerTokenMiddleware;
use SandwaveIo\FSecure\BearerTokenMiddlewareRestClientFactory;
use SandwaveIo\FSecure\Client\AuthRestClientInterface;

use function PHPUnit\Framework\assertInstanceOf;

final class BearerTokenMiddlewareRestClientFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $clientFactory = new BearerTokenMiddlewareRestClientFactory(
            'url',
            new BearerTokenMiddleware(
                $this->createMock(AuthRestClientInterface::class)
            )
        );
        $client = $clientFactory->create();

        assertInstanceOf(Client::class, $client);
    }
}
