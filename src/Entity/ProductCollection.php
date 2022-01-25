<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure\Entity;

use JMS\Serializer\Annotation as Serializer;

final class ProductCollection
{
    /**
     * @var Product[]
     * @Serializer\Type("array<SandwaveIo\FSecure\Entity\Product>")
     * @Serializer\SerializedName("items")
     */
    public array $items;
}
