<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;

final class BearerTokenMiddlewareGuzzleClientFactory implements GuzzleClientFactoryInterface
{
    private string $url;

    private BearerTokenMiddleware $middleWare;

    public function __construct(string $url, BearerTokenMiddleware $middleWare)
    {
        $this->url = $url;
        $this->middleWare = $middleWare;
    }

    public function create(): ClientInterface
    {
        $config = ['base_uri' => $this->url];
        $stack = HandlerStack::create();
        $stack->push($this->middleWare);

        $config['handler'] = $stack;
        $config['auth'] = 'oauth';

        return new Client($config);
    }
}
