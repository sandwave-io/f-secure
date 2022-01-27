<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure;

use Closure;
use Psr\Http\Message\RequestInterface;
use SandwaveIo\FSecure\Client\AuthRestClientInterface;
use SandwaveIo\FSecure\Entity\AccessToken;

final class BearerTokenMiddleware
{
    private ?AccessToken $accessToken = null;

    private AuthRestClientInterface $restClient;

    public function __construct(AuthRestClientInterface $restClient)
    {
        $this->restClient = $restClient;
    }

    public function __invoke(callable $handler): Closure
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            if ($this->accessToken === null) {
                $this->accessToken = $this->restClient->getToken();
            }

            return $handler(
                $request->withAddedHeader('Authorization', $this->accessToken->tokenType . ' ' . $this->accessToken->accessToken),
                $options
            );
        };
    }
}
