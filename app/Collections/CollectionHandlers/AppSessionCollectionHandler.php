<?php

declare(strict_types=1);

namespace App\Collections\CollectionHandlers;

use App\Handlers\AuthSessionAttributesHandler;

/**
 * class AppSessionCollectionHandler
 * @package App\Collections\CollectionHandlers
 */
class AppSessionCollectionHandler
{
    public AuthSessionAttributesHandler $attributesHandler;

    public function __construct(AuthSessionAttributesHandler $attributesHandler)
    {
        $this->attributesHandler = $attributesHandler;
    }
}
