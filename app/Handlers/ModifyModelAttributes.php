<?php

declare(strict_types=1);

namespace App\Handlers;

use App\Handlers\Contracts\ModifyModelAttributesInterface;
use App\Http\Resources\PostResource;

/**
 * class ModifyModelAttributesInterface
 * @package App\Handlers
 */
class ModifyModelAttributes implements ModifyModelAttributesInterface
{
    /**
     * @param array $attributes
     * @param iterable $modifyAttributes
     * @return array
     */
    public function execute(array $attributes, iterable $modifyAttributes): array
    {
        foreach ($modifyAttributes as $newKey => $oldKey) {
            if (array_key_exists($newKey, $attributes)) {
                if ($newKey == 'post_privacy') {
                    $attributes[$newKey] = (string)$attributes[$newKey];
                }
                $attributes[$oldKey] = $attributes[$newKey];
                unset($attributes[$newKey]);
            }
        }

        return $attributes;
    }
}
