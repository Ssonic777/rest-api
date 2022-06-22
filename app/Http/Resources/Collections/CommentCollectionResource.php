<?php

declare(strict_types=1);

namespace App\Http\Resources\Collections;

use App\Http\Resources\CommentResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * class CommentCollectionResource
 * @package App\Http\ResourceCollection
 */
class CommentCollectionResource extends ResourceCollection
{

    /**
     * @var string $collects
     */
    public $collects = CommentResource::class;

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
