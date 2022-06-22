<?php

declare(strict_types=1);

namespace App\Http\Resources\Collections;

use App\Http\Resources\GroupAdminResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * class GroupAdminCollectionResource
 * @package App\Http\Resources\Collections
 */
class GroupAdminCollectionResource extends ResourceCollection
{

    public $collects = GroupAdminResource::class;

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
