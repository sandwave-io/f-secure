<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure\Service;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\TooManyRedirectsException;
use GuzzleHttp\Exception\TransferException;
use SandwaveIo\FSecure\Exception\BadRequestException;
use SandwaveIo\FSecure\Exception\FsecureException;
use SandwaveIo\FSecure\Exception\NetworkException;
use SandwaveIo\FSecure\Exception\ResourceNotFoundException;
use SandwaveIo\FSecure\Exception\ServerException as FSecureServerException;
use SandwaveIo\FSecure\Exception\UnauthorizedException;
use SandwaveIo\FSecure\Exception\UnknownException;
use Throwable;

final class ThrowableConvertor
{
    /**
     * @param Throwable $exception
     * @return FsecureException
     */
    public function convert(Throwable $exception): FsecureException
    {
        $message = $exception instanceof RequestException ? $this->convertMessage($exception) : $exception->getMessage(
        );

        if(!$exception instanceof TransferException) {
            return new UnknownException($message, 0, $exception);
        }

        if ($exception instanceof ConnectException || $exception instanceof TooManyRedirectsException) {
            return new NetworkException($message, 0, $exception);
        }

        if ($exception instanceof ServerException) {
            // error 500 range
            return new FSecureServerException($message, 0, $exception);
        }

        if ($exception instanceof ClientException) {
            if ($exception->getCode() === 404) {
                return new ResourceNotFoundException($message, 0, $exception);
            }

            if ($exception->getCode() === 401) {
                return new UnauthorizedException($message, 0, $exception);
            }

            // 400 range
            return new BadRequestException($message, 0, $exception);
        }

        return new UnknownException($message, 0, $exception);
    }

    private function convertMessage(RequestException $exception): string
    {
        $response = $exception->getResponse();

        if (null === $response) {
            return $exception->getMessage();
        }

        return $response->getBody()->getContents();
    }
}
