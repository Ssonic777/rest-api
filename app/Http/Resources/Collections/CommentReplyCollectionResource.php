<?php

declare(strict_types=1);

namespace App\Http\Resources\Collections;

use App\Http\Resources\CommentReplyResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * class CommentReplyCollectionResource
 * @package App\Http\Resources\Collections
 */
class CommentReplyCollectionResource extends ResourceCollection
{

    /**
     * @var string $collects
     */
    public $collects = CommentReplyResource::class;

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
