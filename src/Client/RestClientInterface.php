<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure\Client;

use SandwaveIo\FSecure\Exception\FsecureException;

interface RestClientInterface
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
     * @throws FsecureException
     */
    public function getRawData(string $url): string;

    /**
     * @template T
     *
     * @param string          $url
     * @param object          $data
     * @param class-string<T> $returnType
     *
     * @return T
     */
    public function post(string $url, object $data, string $returnType);
}
