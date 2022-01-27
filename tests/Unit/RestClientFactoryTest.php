<?php

declare(strict_types=1);

namespace SandwaveIo\Acronis\Tests\Unit;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use SandwaveIo\FSecure\RestClientFactory;

use function PHPUnit\Framework\assertInstanceOf;

final class RestClientFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $clientFactory = new RestClientFactory('url');
        $client = $clientFactory->create();

        assertInstanceOf(Client::class, $client);
    }
}
