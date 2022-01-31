<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure\Tests\Unit\Service;

use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\TooManyRedirectsException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use SandwaveIo\FSecure\Exception\BadRequestException;
use SandwaveIo\FSecure\Exception\NetworkException;
use SandwaveIo\FSecure\Exception\ResourceNotFoundException;
use SandwaveIo\FSecure\Exception\ServerException as FSecureServerException;
use SandwaveIo\FSecure\Exception\UnauthorizedException;
use SandwaveIo\FSecure\Exception\UnknownException;
use SandwaveIo\FSecure\Service\ThrowableConvertor;

final class ExceptionConvertorTest extends TestCase
{
    public function testConvertRequestException(): void
    {
        $convertor = new ThrowableConvertor();
        $resp = $convertor->convert(
            new RequestException(
                'fake',
                new Request(
                    'GET',
                    'fakeUrl'
                ),
                new Response(
                    401,
                    [],
                    'fakeBody'
                )
            )
        );

        self::assertInstanceOf(UnknownException::class, $resp);
    }

    public function testConvertRequestException2(): void
    {
        $convertor = new ThrowableConvertor();
        $resp = $convertor->convert(
            new RequestException(
                'fake',
                new Request(
                    'GET',
                    'fakeUrl'
                )
            )
        );

        self::assertInstanceOf(UnknownException::class, $resp);
    }

    public function testConvertConnectException(): void
    {
        $convertor = new ThrowableConvertor();
        $resp = $convertor->convert(
            new ConnectException(
                'fake',
                new Request(
                    'GET',
                    'fakeUrl'
                )
            )
        );

        self::assertInstanceOf(NetworkException::class, $resp);
    }

    public function testConvertTooManyRedirectsException(): void
    {
        $convertor = new ThrowableConvertor();
        $resp = $convertor->convert(
            new TooManyRedirectsException(
                'fake',
                new Request(
                    'GET',
                    'fakeUrl'
                )
            )
        );

        self::assertInstanceOf(NetworkException::class, $resp);
    }

    public function testConvertServerException(): void
    {
        $convertor = new ThrowableConvertor();
        $resp = $convertor->convert(
            new ServerException(
                'fake',
                new Request(
                    'GET',
                    'fakeUrl'
                ),
                new Response(
                    500
                )
            )
        );

        self::assertInstanceOf(FSecureServerException::class, $resp);
    }

    public function testConvertClientExceptionNotFound(): void
    {
        $convertor = new ThrowableConvertor();
        $resp = $convertor->convert(
            new ClientException(
                'fake',
                new Request(
                    'GET',
                    'fakeUrl'
                ),
                new Response(
                    404
                )
            )
        );

        self::assertInstanceOf(ResourceNotFoundException::class, $resp);
    }

    public function testConvertClientExceptionUnauthorized(): void
    {
        $convertor = new ThrowableConvertor();
        $resp = $convertor->convert(
            new ClientException(
                'fake',
                new Request(
                    'GET',
                    'fakeUrl'
                ),
                new Response(
                    401
                )
            )
        );

        self::assertInstanceOf(UnauthorizedException::class, $resp);
    }

    public function testConvertClientExceptionBadRequest(): void
    {
        $convertor = new ThrowableConvertor();
        $resp = $convertor->convert(
            new ClientException(
                'fake',
                new Request(
                    'GET',
                    'fakeUrl'
                ),
                new Response(
                    402
                )
            )
        );

        self::assertInstanceOf(BadRequestException::class, $resp);
    }

    public function testConvertUnknownThrowable(): void
    {
        $convertor = new ThrowableConvertor();
        $resp = $convertor->convert(
            new Exception(
                'fake',
            )
        );

        self::assertInstanceOf(UnknownException::class, $resp);
    }
}
