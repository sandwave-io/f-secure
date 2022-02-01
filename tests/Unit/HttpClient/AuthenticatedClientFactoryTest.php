<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure\Tests\Unit\HttpClient;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use SandwaveIo\FSecure\Client\AuthClientInterface;
use SandwaveIo\FSecure\HttpClient\AuthenticatedClientFactory;
use SandwaveIo\FSecure\HttpClient\BearerTokenMiddleware;

use function PHPUnit\Framework\assertInstanceOf;

final class AuthenticatedClientFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $clientFactory = new AuthenticatedClientFactory(
            'url',
            new BearerTokenMiddleware(
                $this->createMock(AuthClientInterface::class)
            )
        );
        $client = $clientFactory->create();

        assertInstanceOf(Client::class, $client);
    }
}
