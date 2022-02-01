<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure;

use Closure;
use Psr\Http\Message\RequestInterface;
use SandwaveIo\FSecure\Client\AuthClientInterface;
use SandwaveIo\FSecure\Entity\AccessToken;

final class BearerTokenMiddleware
{
    private ?AccessToken $accessToken = null;

    private AuthClientInterface $client;

    public function __construct(AuthClientInterface $client)
    {
        $this->client = $client;
    }

    public function __invoke(callable $handler): Closure
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            if ($this->accessToken === null) {
                $this->accessToken = $this->client->getToken();
            }

            return $handler(
                $request->withAddedHeader('Authorization', $this->accessToken->tokenType . ' ' . $this->accessToken->accessToken),
                $options
            );
        };
    }
}
