<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure;

use SandwaveIo\FSecure\Client\OrderClient;
use SandwaveIo\FSecure\Client\ProductClient;
use SandwaveIo\FSecure\Client\ClientInterface;

final class FsecureClient
{
    private ProductClient $productClient;

    private OrderClient $orderClient;

    public function __construct(ClientInterface $restClient)
    {
        $this->setClient($restClient);
    }

    public function getProductClient(): ProductClient
    {
        return $this->productClient;
    }

    public function getOrderClient(): OrderClient
    {
        return $this->orderClient;
    }

    private function setClient(ClientInterface $client): void
    {
        $this->productClient = new ProductClient($client);
        $this->orderClient = new OrderClient($client);
    }
}
