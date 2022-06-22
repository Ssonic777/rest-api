<?php

namespace App\Handlers\Contracts;

use App\Handlers\ModelAttributes;
use Illuminate\Database\Eloquent\Model;

interface ModelDeleteAttributesInterface
{
    /**
     * @param Model $model
     * @param ModelAttributes $modelAttributes
     */
    public function execute(Model $model, ModelAttributes $modelAttributes): void;
}
