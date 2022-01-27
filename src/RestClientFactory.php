<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

final class RestClientFactory implements RestClientFactoryInterface
{
    private string $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function create(): ClientInterface
    {
        return new Client(['base_uri' => $this->url]);
    }
}
