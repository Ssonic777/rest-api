<?php

declare(strict_types=1);

namespace App\Http\Resources\Collections;

use App\Http\Resources\GroupResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * class GroupCollectionResource
 * @package App\Http\Resources;
 */
class GroupCollectionResource extends ResourceCollection
{

    public $collects = GroupResource::class;

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
