<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\Group\GroupAdditionalDataResource;
use App\Http\Resources\Group\GroupAdminResource;
use App\Http\Resources\Group\GroupCategoryResource;
use App\Traits\AutoloadAttributeJsonResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * class GroupResource
 * @package App\Http\Resources
 */
class GroupResource extends JsonResource
{
    use AutoloadAttributeJsonResource;

    protected function makeModifyAttributes(): array
    {
        return [
            'owner_id' => 'user_id',
            'group_slug' => 'group_name',
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

    protected function mergeAttributes(): array
    {
        return [
            'category' => GroupCategoryResource::make($this->whenLoaded('catry')),
            'setting' => GroupAdditionalDataResource::make($this->whenLoaded('setting')),
            'permissions' => GroupAdminResource::make($this->whenLoaded('admin_permissions'))
        ];
    }
}
