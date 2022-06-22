<?php

declare(strict_types=1);

namespace App\Http\Resources\Collections;

use App\Http\Resources\BlogResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * class BlogCollectionResource
 * @package App\Http\Resources\Collections
 */
class BlogCollectionResource extends ResourceCollection
{

    /**
     * @var string $collects
     */
    public $collects = BlogResource::class;

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
