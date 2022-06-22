<?php

declare(strict_types=1);

namespace App\Http\Resources\Group;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * class GroupAdminResource
 * @package App\Http\Resource\Group
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
            'privilege_settings' => [
                ['key' => 'general', 'value' => $this->general],
                ['key' => 'privacy', 'value' => $this->privacy],
                ['key' => 'avatar', 'value' => $this->avatar],
                ['key' => 'members', 'value' => $this->members],
                ['key' => 'analytics', 'value' => $this->analytics],
                ['key' => 'delete_group', 'value' => $this->delete_group],
            ]
        ];
    }
}
