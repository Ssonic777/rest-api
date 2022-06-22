<?php

declare(strict_types=1);

namespace App\Http\Resources\Group;

use App\Http\Resources\MessageResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class GroupChatUserResource
 * @package App\Http\Resources\Group
 */
class GroupChatUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'group_id' => $this->group_id,
            'group' => GroupChatResource::make($this->whenLoaded('group')),
            'active' => $this->active,
            'last_seen' => $this->last_seen,
            'messages' => MessageResource::collection($this->whenLoaded('messages'))
        ];
    }
}
