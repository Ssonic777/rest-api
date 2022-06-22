<?php

declare(strict_types=1);

namespace App\Collections;

use App\Collections\CollectionHandlers\AppSessionCollectionHandler;
use App\Models\AuthRefreshToken;
use Illuminate\Support\Collection;

/**
 * class AppSessionCollection
 * @package App\Collections
 */
class AppSessionCollection extends Collection
{
    private AppSessionCollectionHandler $handler;

    public function __construct($items = [])
    {
        parent::__construct($items);
        $this->handler = resolve(AppSessionCollectionHandler::class);
    }

    /**
     * @return $this
     */
    public function setAttributes(): self
    {
        return $this->each(function (AuthRefreshToken $authRefreshToken): void {
            $this->handler->attributesHandler->execute($authRefreshToken);
        });
    }
}
