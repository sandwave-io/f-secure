<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure\Client;

use GuzzleHttp\RequestOptions;
use JMS\Serializer\SerializerInterface;
use SandwaveIo\FSecure\Entity\AccessToken;
use SandwaveIo\FSecure\Exception\FsecureException;
use GuzzleHttp\ClientInterface;
use SandwaveIo\FSecure\Service\ThrowableConvertor;
use Throwable;

final class AuthClient implements AuthClientInterface
{
    private const GET_TOKEN = 'oauth2/token';
    private const REQUEST_TIMEOUT = 5;

    private ClientInterface $client;

    private string $clientId;

    private string $clientSecret;

    private SerializerInterface $serializer;

    private ThrowableConvertor $exceptionConvertor;

    public function __construct(
        string $clientId,
        string $clientSecret,
        ClientInterface $client,
        SerializerInterface $serializer,
        ThrowableConvertor $exceptionConvertor
    ) {
        $this->client = $client;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->serializer = $serializer;
        $this->exceptionConvertor = $exceptionConvertor;
    }

    /**
     * @throws FsecureException
     *
     * @return AccessToken
     */
    public function getToken(): AccessToken
    {
        try {
            $response = $this->client->request('POST', self::GET_TOKEN, [
                RequestOptions::FORM_PARAMS => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'scope' => 'license_orders_write license_orders_read license_products_read',
                ],
                RequestOptions::HEADERS => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Accept' => 'application/json',
                ],
                RequestOptions::CONNECT_TIMEOUT => self::REQUEST_TIMEOUT,
            ]);
        } catch (Throwable $exception) {
            throw $this->exceptionConvertor->convert($exception);
        }

        return $this->serializer->deserialize($response->getBody()->getContents(), AccessToken::class, 'json');
    }
}
