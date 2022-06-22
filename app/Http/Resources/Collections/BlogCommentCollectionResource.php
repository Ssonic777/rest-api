<?php

declare(strict_types=1);

namespace App\Http\Resources\Collections;

use App\Http\Resources\BlogCommentResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * class CommentCollectionResource
 * @package App\Http\ResourceCollection
 */
class BlogCommentCollectionResource extends ResourceCollection
{

    /**
     * @var string $collects
     */
    public $collects = BlogCommentResource::class;

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
