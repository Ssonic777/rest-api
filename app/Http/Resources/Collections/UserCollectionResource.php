<?php

declare(strict_types=1);

namespace App\Http\Resources\Collections;

use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * class UserCollectionResource
 * @package App\Http\Resources\Collections
 */
class UserCollectionResource extends ResourceCollection
{

    public $collects = UserResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return $this->resource->toArray();
    }
}
