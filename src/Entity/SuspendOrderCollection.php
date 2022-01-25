<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure\Entity;

use JMS\Serializer\Annotation as Serializer;

final class SuspendOrderCollection
{
    /**
     * @var SuspendOrder[]
     * @Serializer\Type("array<SandwaveIo\FSecure\Entity\SuspendOrder>")
     * @Serializer\SerializedName("items")
     */
    public array $items;
}
