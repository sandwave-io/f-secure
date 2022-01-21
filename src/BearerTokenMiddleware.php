<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure;

use Closure;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use Psr\Http\Message\RequestInterface;

final class BearerTokenMiddleware
{
    private ?string $bearerToken = null;

    private string $url;

    private string $clientId;

    private string $clientSecret;

    public function __construct(string $url, string $clientId, string $clientSecret)
    {
        $this->url = $url;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    public function __invoke(callable $handler): Closure
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            if ($this->bearerToken === null) {
                $response = $this->getBearerToken();
                $this->bearerToken = $response->access_token;
            }

            return $handler(
                $request->withAddedHeader('Authorization', 'Bearer ' . $this->bearerToken),
                $options
            );
        };
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     *
     * @return mixed
     *
     */
    private function getBearerToken()
    {
        $client = new Client(['base_uri' => $this->url]);
        $response = $client->post(
            'oauth2/token',
            [
                'headers'     => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Accept'       => 'application/json',
                ],
                'form_params' => [
                    'grant_type'    => 'client_credentials',
                    'client_id'     => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'scope'         => 'license_orders_write license_orders_read license_products_read',
                ],
            ]
        );

        return json_decode($response->getBody()->getContents(), false, 512, JSON_THROW_ON_ERROR);
    }
}
