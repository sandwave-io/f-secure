<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure\Client;

use Exception;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\TooManyRedirectsException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\RequestOptions;
use JMS\Serializer\SerializerInterface;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use SandwaveIo\FSecure\Exception\BadRequestException;
use SandwaveIo\FSecure\Exception\DeserializationException;
use SandwaveIo\FSecure\Exception\FsecureException;
use SandwaveIo\FSecure\Exception\NetworkException;
use SandwaveIo\FSecure\Exception\ResourceNotFoundException;
use SandwaveIo\FSecure\Exception\ServerException as FSecureServerException;
use SandwaveIo\FSecure\Exception\UnauthorizedException;
use SandwaveIo\FSecure\Exception\UnknownException;

final class RestClient implements RestClientInterface
{
    private const REQUEST_TIMEOUT = 5;

    private ClientInterface $client;

    private SerializerInterface $serializer;

    public function __construct(ClientInterface $client, SerializerInterface $serializer)
    {
        $this->client = $client;
        $this->serializer = $serializer;
    }

    /**
     * @template T of object
     *
     * @param string          $url
     * @param class-string<T> $returnType
     *
     * @throws FSecureException|GuzzleException
     *
     * @return T
     *
     */
    public function getEntity(string $url, string $returnType): object
    {
        $this->assertValidClass($returnType);
        $json = $this->get($url);

        return $this->serializer->deserialize($json, $returnType, 'json');
    }

    /**
     * @param string $url
     *
     * @throws GuzzleException
     *
     * @return string
     */
    public function getRawData(string $url): string
    {
        return $this->get($url);
    }

    /**
     * @template T
     *
     * @param string          $url
     * @param object          $data
     * @param class-string<T> $returnType
     *
     * @throws GuzzleException
     *
     * @return T
     */
    public function post(string $url, object $data, string $returnType)
    {
        $this->assertValidClass($returnType);
        $json = $this->serializer->serialize($data, 'json');

        $response = $this->request('POST', $url, [
            'body'    => $json,
            'headers' => [
                'Content-type' => 'application/json; charset=utf-8',
            ],
        ]);

        return $this->serializer->deserialize($response->getBody()->getContents(), $returnType, 'json');
    }

    /**
     * @throws GuzzleException
     */
    private function get(string $url): string
    {
        $response = $this->request('GET', $url);

        return $response->getBody()->getContents();
    }

    /**
     * @param string               $method
     * @param string               $url
     * @param array<string, mixed> $options
     *
     * @throws FSecureException|GuzzleException
     *
     * @return ResponseInterface
     *
     */
    private function request(string $method, string $url, array $options = []): ResponseInterface
    {
        try {
            $response = $this->client->request($method, $url, array_merge($options, $this->getRequestOptions()));
        } catch (TransferException $exception) {
            throw $this->convertException($exception);
        }

        return $response;
    }

    /**
     * @return array<string, int>
     */
    private function getRequestOptions(): array
    {
        return [
            RequestOptions::CONNECT_TIMEOUT => self::REQUEST_TIMEOUT,
        ];
    }

    /**
     * @template T
     *
     * @param class-string<T> $className
     */
    private function assertValidClass(string $className): void
    {
        if (! class_exists($className)) {
            throw new DeserializationException(sprintf('Supplied classname %s does not exist', $className));
        }
    }

    private function convertException(Exception $exception): FsecureException
    {
        $message = $exception instanceof RequestException ? $this->convertMessage($exception) : $exception->getMessage(
        );

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

        $body = $response->getBody()->getContents();
        try {
            $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $decoded = null;
        }

        if (null === $decoded) {
            return $exception->getMessage();
        }

        if (array_key_exists('error', $decoded)) {
            if (
                array_key_exists('details', $decoded['error'])
                && array_key_exists('info', $decoded['error']['details'])
                && is_string($decoded['error']['details']['info'])) {
                return $decoded['error']['details']['info'];
            }

            if (array_key_exists('message', $decoded['error']) && is_string($decoded['error']['message'])) {
                return $decoded['error']['message'];
            }
        }

        if (array_key_exists('error_description', $decoded) && is_string($decoded['error_description'])) {
            return $decoded['error_description'];
        }

        return $exception->getMessage();
    }
}
