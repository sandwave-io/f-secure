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
use SandwaveIo\FSecure\BearerTokenMiddleware;
use SandwaveIo\FSecure\BearerTokenMiddlewareRestClientFactory;
use SandwaveIo\FSecure\Client\AuthClient;
use SandwaveIo\FSecure\Client\RestClient;
use SandwaveIo\FSecure\FsecureClient;
use SandwaveIo\FSecure\RestClientFactory;

$apiEndpoint = 'https://vip.f-secure.com/api/v2/';
$clientId = 'client_id';
$clientSecret = 'client_secret';

$serializerBuilder = new SerializerBuilder();

$factory = new RestClientFactory(
    $apiEndpoint,
);

$authClient = new AuthClient(
    $clientId,
    $clientSecret,
    $factory->create(),
    $serializerBuilder->build(),
    new SandwaveIo\FSecure\Service\ThrowableConvertor()
);

$factory = new BearerTokenMiddlewareRestClientFactory(
    $apiEndpoint,
    new BearerTokenMiddleware($authClient)
);

$serializerBuilder = new SerializerBuilder();
$restClient = new RestClient(
    $factory->create(),
    $serializerBuilder->setPropertyNamingStrategy(
        new SerializedNameAnnotationStrategy(
            new IdenticalPropertyNamingStrategy()
        )
    )->build(),
    new SandwaveIo\FSecure\Service\ThrowableConvertor()
);

$fsecureApi = new FsecureClient($restClient);
$products = $fsecureApi->getProductClient()->get();
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
