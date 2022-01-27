<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure\Client;

use SandwaveIo\FSecure\Entity\AccessToken;
use SandwaveIo\FSecure\Exception\FsecureException;

interface AuthRestClientInterface
{
    /**
     * @throws FsecureException
     */
    public function getToken(): AccessToken;
}
