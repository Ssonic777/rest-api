<?php

declare(strict_types=1);

namespace App\Services\ServiceHandlers;

use App\Handlers\AuthSessionAttributesHandler;

/**
 * class SessionServiceHandler
 * @package App\Services\ServiceHandlers
 */
class SessionServiceHandler
{
    public AuthSessionAttributesHandler $attributesHandler;

    public function __construct(AuthSessionAttributesHandler $attributesHandler)
    {
        $this->attributesHandler = $attributesHandler;
    }
}
