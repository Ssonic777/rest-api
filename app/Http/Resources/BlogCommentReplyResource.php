<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Traits\AutoloadAttributeJsonResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * class BlogCommentReplyResource
 * @package App\Http\Resources
 */
class BlogCommentReplyResource extends JsonResource
{
    use AutoloadAttributeJsonResource;

    public const MODIFY_ATTRIBUTES = [
        'owner_id' => 'user_id',
        'article_id' => 'blog_id',
        'comment_id' => 'comm_id',
        'likes_count' => 'likes',
        'created_at' => 'posted',
    ];

    /**
     * @return array
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
            'comment' => BlogCommentResource::make($this->whenLoaded('comment'))
        ];
    }
}
