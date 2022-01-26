<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure\Entity;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("none")
 */
final class NewOrder
{
    public int $variationId;

    public string $customerReference;

    public int $storeId;

    public ?string $language = null;

    public ?string $customerName = null;

    public ?string $customerSurname = null;

    public ?string $customerEmail = null;

    public function __construct(
        int $variationId,
        string $customerReference,
        int $storeId
    ) {
        $this->variationId = $variationId;
        $this->customerReference = $customerReference;
        $this->storeId = $storeId;
    }
}
