<?php

declare(strict_types=1);

namespace App\Http\Resources\Group;

use App\Http\Resources\MessageResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class GroupChatResource
 * @package App\Http\Resources\Group
 */
class GroupChatResource extends JsonResource
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
            'group_id' => $this->group_id,
            'user_id' => $this->user_id,
            'group_name' => $this->group_name,
            'avatar' => $this->avatar,
            'admin' => UserResource::make($this->whenLoaded('admin')),
            'users' => UserResource::collection($this->whenLoaded('users')),
            'active' => $this->active,
            'messages' => MessageResource::collection($this->whenLoaded('messages')) ,
            'time' => $this->time,
            'group_public_id' => $this->group_public_id,
        ];
    }
}
