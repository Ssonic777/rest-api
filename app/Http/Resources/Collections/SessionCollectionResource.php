<?php

declare(strict_types=1);

namespace App\Http\Resources\Collections;

use App\Http\Resources\SessionResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

/**
 * class SessionCollectionResource
 * @package \App\Http\Resources\Collections
 *
 * @property Collection $resource
 */
class SessionCollectionResource extends ResourceCollection
{

    public $collects = SessionResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array
    {
        return $this->resource->toArray();
    }
}
