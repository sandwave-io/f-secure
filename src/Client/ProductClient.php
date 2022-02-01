<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure\Client;

use SandwaveIo\FSecure\Entity\ProductCollection;

final class ProductClient
{
    private const AVAILABLE_PRODUCTS = 'licenses/get_available_products';

    private ClientInterface $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function get(): ProductCollection
    {
        return $this->client->getEntity(self::AVAILABLE_PRODUCTS, ProductCollection::class);
    }
}
