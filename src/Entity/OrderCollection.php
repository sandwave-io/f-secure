<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure\Entity;

use JMS\Serializer\Annotation as Serializer;

final class OrderCollection
{
    /**
     * @var Order[]
     *
     * @Serializer\Type("array<SandwaveIo\FSecure\Entity\Order>")
     *
     * @Serializer\SerializedName("items")
     */
    public array $items;
}
