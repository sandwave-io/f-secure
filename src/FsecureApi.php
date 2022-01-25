<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure;

use SandwaveIo\FSecure\Client\ProductClient;
use SandwaveIo\FSecure\Client\RestClientInterface;

final class FsecureApi
{
    private ProductClient $productClient;

    public function __construct(RestClientInterface $restClient)
    {
        $this->setClient($restClient);
    }

    public function getProductClient(): ProductClient
    {
        return $this->productClient;
    }

    private function setClient(RestClientInterface $client): void
    {
        $this->productClient = new ProductClient($client);
    }
}
