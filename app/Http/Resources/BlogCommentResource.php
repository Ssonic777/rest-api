<?php

namespace App\Http\Resources;

use App\Traits\AutoloadAttributeJsonResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * class BlogCommentResource
 * @package App\Http\Resources
 */
class BlogCommentResource extends JsonResource
{
    use AutoloadAttributeJsonResource;

    public const MODIFY_ATTRIBUTES = [
        'owner_id' => 'user_id',
        'file' => 'c_file',
        'likes_count' => 'reactions_count',
        'created_at' => 'time',
        'article_id' => 'blog_id',
    ];

    /**
     * @return string[]
     */
    protected function makeModifyAttributes(): array
    {
        return self::MODIFY_ATTRIBUTES;
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
            'owner' => UserResource::make($this->whenLoaded('user')),
            'replies' => CommentReplyResource::collection($this->whenLoaded('replies')),
        ];
    }
}
