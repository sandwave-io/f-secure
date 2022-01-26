<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use SandwaveIo\FSecure\RestClientFactory;

use function PHPUnit\Framework\assertInstanceOf;

class RestClientFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $clientFactory = new RestClientFactory('url', 'identifier', 'secret');
        $client = $clientFactory->create();

        assertInstanceOf(Client::class, $client);
    }

}
