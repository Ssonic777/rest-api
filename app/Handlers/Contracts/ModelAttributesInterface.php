<?php

declare(strict_types=1);

namespace App\Handlers\Contracts;

interface ModelAttributesInterface
{
    public function setAttributes(array $attributes): self;
    public function getAttributes(): array;
}
