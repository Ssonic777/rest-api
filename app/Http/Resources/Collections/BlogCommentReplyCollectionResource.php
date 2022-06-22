<?php

declare(strict_types=1);

namespace App\Http\Resources\Collections;

use App\Http\Resources\BlogCommentReplyResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * class BlogCommentReplyCollectionResource
 * @package App\Http\Resources\Json
 */
class BlogCommentReplyCollectionResource extends ResourceCollection
{

    /**
     * @var string $collects
     */
    public $collects = BlogCommentReplyResource::class;

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
