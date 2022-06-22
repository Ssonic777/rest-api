<?php

declare(strict_types=1);

namespace App\Http\Resources\Collections;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\UserResource;

/**
 * class UserCollectionResource
 * @package App\Http\Resources\Collections
 */
class FollowingRequestCollectionResource extends ResourceCollection
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
        $data = $this->resource->toArray();

        if (isset($data['items_count'])) {
            $data['requests_count'] = $data['items_count'];
        }

        return $data;
    }
}