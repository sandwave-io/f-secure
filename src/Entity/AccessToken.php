<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure\Entity;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("none")
 */
final class AccessToken
{
    public string $accessToken;

    public string $tokenType;

    public int $expiresIn;

    public string $scope;
}
