<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * class GroupAdminResource
 * @package App\Http\REsource
 */
class GroupAdminResource extends JsonResource
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
            'user_id' => $this->user_id,
            'group_id' => $this->group_id,
            'general' => $this->general,
            'privacy' => $this->privacy,
            'avatar' => $this->avatar,
            'members' => $this->members,
            'analytics' => $this->analytics,
            'delete_group' => $this->delete_group
        ];
    }
}
