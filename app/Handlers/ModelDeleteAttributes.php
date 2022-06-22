<?php

declare(strict_types=1);

namespace App\Handlers;

use App\Handlers\Contracts\ModelDeleteAttributesInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * class ModelDeleteAttributes
 * @package App\Handlers
 */
class ModelDeleteAttributes implements ModelDeleteAttributesInterface
{
    /**
     * @param Model $model
     * @param ModelAttributes $modelAttributes
     */
    public function execute(Model $model, ModelAttributes $modelAttributes): void
    {
        foreach ($modelAttributes->getAttributes() as $key => $attribute) {
            $model->offsetUnset($attribute);
        }
    }
}
