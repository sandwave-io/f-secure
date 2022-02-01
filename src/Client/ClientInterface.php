<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure\Client;

use SandwaveIo\FSecure\Exception\FsecureException;

interface ClientInterface
{
    /**
     * @template T of object
     *
     * @param string          $url
     * @param class-string<T> $returnType
     *
     * @throws FsecureException
     *
     * @return T
     */
    public function getEntity(string $url, string $returnType): object;

    /**
     * @template T of object
     *
     * @param string          $url
     * @param object          $data
     * @param class-string<T> $returnType
     *
     * @return T
     */
    public function post(string $url, object $data, string $returnType): object;
}
