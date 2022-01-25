<?php

declare(strict_types=1);

namespace SandwaveIo\FSecure\Entity;

use JMS\Serializer\Annotation as Serializer;

final class NewOrder
{
    /**
     * @Serializer\Groups({"create_data"})
     */
    public int $variationId;

    /**
     * @Serializer\Groups({"create_data"})
     */
    public string $customerReference;

    /**
     * @Serializer\Groups({"create_data"})
     */
    public int $storeId;

    /**
     * @Serializer\Groups({"create_data"})
     */
    public ?string $language = null;

    /**
     * @Serializer\Groups({"create_data"})
     */
    public ?string $customerName = null;

    /**
     * @Serializer\Groups({"create_data"})
     */
    public ?string $customerSurname = null;

    /**
     * @Serializer\Groups({"create_data"})
     */
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
