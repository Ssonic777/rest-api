<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Blog;
use App\Traits\AutoloadAttributeJsonResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * class BlogResource
 * @property Blog $resource
 * @package App\Http\Resources
 */
class BlogResource extends JsonResource
{
    use AutoloadAttributeJsonResource;

    /**
     * @return array
     */
    protected function makeModifyAttributes(): array
    {
        return [
            'owner_id' => 'user',
            'created_at' => 'posted',
            'category_id' => 'category',
        ];
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return $this->parse();
    }

    /**
     * @return array
     */
    public function mergeAttributes(): array
    {
        $attributes = [
            'owner' => UserResource::make($this->whenLoaded('owner')),
            'category' => BlogCategoryResource::make($this->whenLoaded('catry')),
        ];

        if ($this->resource->relationLoaded('post')) {
            $attributes['comments_count'] = $this->resource->post->comments_count;
            $attributes['likes_count'] = $this->resource->post->reactions_count;
        }

        return $attributes;
    }
}
