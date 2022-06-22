<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Traits\AutoloadAttributeJsonResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PostResource
 * @package App\Http\Resources
 */
class PostResource extends JsonResource
{
    use AutoloadAttributeJsonResource;

    public const MODIFY_ATTRIBUTES = [
        'created_at' => 'time',
        'post_text' => 'postText',
        'post_privacy' => 'postPrivacy',
        'post_link' => 'postLink',
        'likes_count' => 'reactions_count',
        'is_pinned' => 'pin_count',
        'service_gif' => 'postSticker',
    ];

    protected function makeModifyAttributes(): array
    {
        return self::MODIFY_ATTRIBUTES;
    }

    /**
     * @return array
     */
    protected function mergeAttributes(): array
    {
        return [
            'owner' => UserResource::make($this->whenLoaded('user')),
            'parent' => self::make($this->whenLoaded('parent')),
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
}
