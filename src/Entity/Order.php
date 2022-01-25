<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure\Entity;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("none")
 */
final class Order
{
    public string $customerReference;

    public int $storeId;

    /**
     * @var DateTimeImmutable
     * @Serializer\Type("DateTimeImmutable<'Y-m-d H:i:s', '', 'Y-m-d\TH:i:s.vP'>")
     */
    public DateTimeImmutable $created;

    /**
     * @var OrderVariation[]
     * @Serializer\Type("array<SandwaveIo\FSecure\Entity\OrderVariation>")
     */
    public array $variations;

    /**
     * @var OrderVariation[]
     * @Serializer\Type("array<SandwaveIo\FSecure\Entity\OrderVariation>")
     */
    public array $rows;
}
