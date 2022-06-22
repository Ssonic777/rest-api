<?php

declare(strict_types=1);

namespace App\Http\Resources\Collections;

use App\Http\Resources\PostCommentResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * class PostCommentCollectionResource
 * @package App\Http\Resources\Collections
 */
class PostCommentCollectionResource extends ResourceCollection
{

    public $collects = PostCommentResource::class;

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
