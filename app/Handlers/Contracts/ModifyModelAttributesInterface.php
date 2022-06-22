<?php

declare(strict_types=1);

namespace App\Handlers\Contracts;

interface ModifyModelAttributesInterface
{
    public function execute(array $attributes, iterable $modifyAttributes): array;
}
