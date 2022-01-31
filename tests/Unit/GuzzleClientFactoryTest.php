<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure\Tests\Unit;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use SandwaveIo\FSecure\GuzzleClientFactory;

final class GuzzleClientFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $clientFactory = new GuzzleClientFactory('url');
        $client = $clientFactory->create();

        self::assertInstanceOf(Client::class, $client);
    }
}
