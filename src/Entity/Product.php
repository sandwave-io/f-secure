<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure\Entity;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("none")
 */
final class Product
{
    public string $type;

    public int $duration;

    public int $amount;

    public string $sku;

    public string $ean;

    public string $distributorPrice;

    public ?string $resellerPrice;

    public ?string $suggestedRetailPrice;

    public int $variationId;

    public string $sellType;

    public bool $canSell;

    public int $productId;

    public string $productLine;

    public string $productTitle;

    public string $language;
}
