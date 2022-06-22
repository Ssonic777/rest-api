<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Traits\AutoloadAttributeJsonResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * PostCommentResource
 * @package App\Http\Resources
 */
class PostCommentResource extends JsonResource
{
    use AutoloadAttributeJsonResource;

    /**
     * @return string[]
     */
    protected function makeModifyAttributes(): array
    {
        return [
            'owner_id' => 'user_id',
            'likes_count' => 'reactions_count',
            'file' => 'c_file',
            'created_at' => 'time',
        ];
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return $this->parse();
    }

    /**
     * @return array
     */
    protected function mergeAttributes(): array
    {
        return [
            'owner' => \App\Http\Resources\UserResource::make($this->whenLoaded('user')),
            'replies' => PostCommentReplyResource::collection($this->whenLoaded('replies'))
        ];
    }
}
