<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure\Tests\Unit;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use SandwaveIo\FSecure\RestClientFactory;

final class RestClientFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $clientFactory = new RestClientFactory('url');
        $client = $clientFactory->create();

        self::assertInstanceOf(Client::class, $client);
    }
}
