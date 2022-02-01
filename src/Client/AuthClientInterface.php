<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure\Client;

use SandwaveIo\FSecure\Entity\AccessToken;
use SandwaveIo\FSecure\Exception\FSecureException;

interface AuthClientInterface
{
    /**
     * @throws FSecureException
     */
    public function getToken(): AccessToken;
}
