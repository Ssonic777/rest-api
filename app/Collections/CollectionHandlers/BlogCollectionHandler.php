<?php

declare(strict_types=1);

namespace App\Collections\CollectionHandlers;

use App\Handlers\Contracts\ModelAttributesInterface;
use App\Handlers\Contracts\ModelDeleteAttributesInterface;
use App\Handlers\ModelAttributes;
use App\Handlers\ModelDeleteAttributes;

/**
 * class BlogCollectionHandler
 * @package App\Collections\CollectionHandlers
 */
class BlogCollectionHandler
{

    /**
     * @var ModelAttributes $modelAttributes
     */
    public ModelAttributesInterface $modelAttributes;

    /**
     * @var ModelDeleteAttributesInterface $modelDeleteAttributes
     */
    public ModelDeleteAttributesInterface $modelDeleteAttributes;

    public function __construct(
        ModelAttributes $modelAttributes,
        ModelDeleteAttributes $modelDeleteAttributes
    ) {
        $this->modelAttributes = $modelAttributes;
        $this->modelDeleteAttributes = $modelDeleteAttributes;
    }
}
