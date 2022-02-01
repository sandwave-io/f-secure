<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure\Tests\Unit\HttpClient;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use SandwaveIo\FSecure\HttpClient\ClientFactory;

final class ClientFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $clientFactory = new ClientFactory('url');
        $client = $clientFactory->create();

        self::assertInstanceOf(Client::class, $client);
    }
}
