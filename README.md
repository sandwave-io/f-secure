[![](https://user-images.githubusercontent.com/60096509/91668964-54ecd500-eb11-11ea-9c35-e8f0b20b277a.png)](https://sandwave.io)


# F-secure API Client


## How to use (REST API)

```bash
composer require sandwave-io/f-secure
```

```php
<?php

require "vendor/autoload.php";

use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializerBuilder;
use SandwaveIo\FSecure\Client\Client;
use SandwaveIo\FSecure\Client\AuthClient;
use SandwaveIo\FSecure\Client\OrderClient;
use SandwaveIo\FSecure\Client\ProductClient;
use SandwaveIo\FSecure\HttpClient\AuthenticatedClientFactory;
use SandwaveIo\FSecure\HttpClient\BearerTokenMiddleware;
use SandwaveIo\FSecure\HttpClient\ClientFactory;
use SandwaveIo\FSecure\Service\ThrowableConvertor;

$apiEndpoint = 'https://vip.f-secure.com/api/v2/';
$clientId = 'client_id';
$clientSecret = 'client_secret';

// create AuthClient
$auth = new AuthClient(
    $clientId,
    $clientSecret,
    (new ClientFactory($apiEndpoint))->create(),
    (new SerializerBuilder())->build(),
    new ThrowableConvertor()
);

// use AuthClient to create a GuzzleClient binded with middleware for handling the bearer token
$httpClient = (new AuthenticatedClientFactory(
    $apiEndpoint, new BearerTokenMiddleware($auth)
))->create();

// create client by injecting the guzzleclient
$client = new Client(
    $httpClient,
    (new SerializerBuilder())->setPropertyNamingStrategy(
        new SerializedNameAnnotationStrategy(
            new IdenticalPropertyNamingStrategy()
        )
    )->build(),
    new ThrowableConvertor()
);

// use client to create ProductClient
$productClient = new ProductClient($client);
$productCollection = $productClient->get();

// or use client to create OrderClient
$orderClient = new OrderClient($client);
$orderCollection = $orderClient->get();
```

## How to contribute

Feel free to create a PR if you have any ideas for improvements. Or create an issue.

* When adding code, make sure to add tests for it (phpunit).
* Make sure the code adheres to our coding standards (use php-cs-fixer to check/fix).
* Also make sure PHPStan does not find any bugs.

```bash
composer analyze # this will (dry)run php-cs-fixer, phpstan and phpunit

composer phpcs-fix # this will actually let php-cs-fixer run to fix
```

These tools will also run in GitHub actions on PR's and pushes on main.
