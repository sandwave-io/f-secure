<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure\HttpClient;

use GuzzleHttp\ClientInterface;

interface ClientFactoryInterface
{
    public function create(): ClientInterface;
}
