<?php

namespace App\Handlers;

use App\Handlers\Contracts\ModelAttributesInterface;

/**
 * class ModelAttributes
 * @package App\Handlers
 */
class ModelAttributes implements ModelAttributesInterface
{
    /**
     * @var array $attributes
     */
    private array $attributes = [];

    /**
     * @param array $attributes
     * @return $this
     */
    public function setAttributes(array $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
