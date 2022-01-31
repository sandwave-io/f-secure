<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure\Tests\Unit;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use SandwaveIo\FSecure\BearerTokenMiddleware;
use SandwaveIo\FSecure\BearerTokenMiddlewareGuzzleClientFactory;
use SandwaveIo\FSecure\Client\AuthClientInterface;

use function PHPUnit\Framework\assertInstanceOf;

final class BearerTokenMiddlewareRestClientFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $clientFactory = new BearerTokenMiddlewareGuzzleClientFactory(
            'url',
            new BearerTokenMiddleware(
                $this->createMock(AuthClientInterface::class)
            )
        );
        $client = $clientFactory->create();

        assertInstanceOf(Client::class, $client);
    }
}
