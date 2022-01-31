<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure\Client;

use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use GuzzleHttp\RequestOptions;
use JMS\Serializer\SerializerInterface;
use Psr\Http\Message\ResponseInterface;
use SandwaveIo\FSecure\Exception\DeserializationException;
use SandwaveIo\FSecure\Exception\FsecureException;
use SandwaveIo\FSecure\Service\ThrowableConvertor;
use Throwable;

final class Client implements ClientInterface
{
    private const REQUEST_TIMEOUT = 5;

    private GuzzleClientInterface $client;

    private SerializerInterface $serializer;

    private ThrowableConvertor $exceptionConvertor;

    public function __construct(GuzzleClientInterface $client, SerializerInterface $serializer, ThrowableConvertor $exceptionConvertor)
    {
        $this->client = $client;
        $this->serializer = $serializer;
        $this->exceptionConvertor = $exceptionConvertor;
    }

    /**
     * @template T of object
     *
     * @param string          $url
     * @param class-string<T> $returnType
     *
     * @throws FSecureException
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
     * @template T
     *
     * @param string          $url
     * @param object          $data
     * @param class-string<T> $returnType
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
     * @param string $url
     *
     * @return string
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
     * @throws FSecureException
     *
     * @return ResponseInterface
     */
    private function request(string $method, string $url, array $options = []): ResponseInterface
    {
        try {
            $response = $this->client->request($method, $url, array_merge($options, $this->getRequestOptions()));
        } catch (Throwable $exception) {
            throw $this->exceptionConvertor->convert($exception);
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
}
