<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure\Tests\Unit\HttpClient;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use SandwaveIo\FSecure\Client\AuthClientInterface;
use SandwaveIo\FSecure\Entity\AccessToken;
use SandwaveIo\FSecure\HttpClient\BearerTokenMiddleware;

final class BearerTokenMiddlewareTest extends TestCase
{
    public function testInvoke(): void
    {
        $accessToken = new AccessToken();
        $accessToken->expiresIn = 3599;
        $accessToken->tokenType = 'Bearer';
        $accessToken->scope = 'fakeScope';
        $accessToken->accessToken = 'fakeToken';

        $authClient = $this->createMock(AuthClientInterface::class);
        $authClient
            ->expects(self::once())
            ->method('getToken')
            ->willReturn($accessToken);

        $mockHandler = new MockHandler(
            [
                new Response(200, [], null),
                new Response(200, [], null),
            ]
        );

        $middleWare = new BearerTokenMiddleware($authClient);
        $stack = HandlerStack::create($mockHandler);
        $closure = $middleWare->__invoke($stack);
        $request = new Request('GET', 'fakeuri');
        $closure(
            $request,
            []
        );

        /** @var Request $lastRequest */
        $lastRequest = $mockHandler->getLastRequest();
        self::assertTrue($lastRequest->hasHeader('Authorization'));
        self::assertContains('Bearer fakeToken', $lastRequest->getHeader('Authorization'));

        $closure(
            $request,
            []
        );
    }
}
