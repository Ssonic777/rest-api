<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class GroupCategoryResource
 * @package App\Http\Resources
 */
class GroupCategoryResource extends JsonResource
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
            'id'    => $this->id,
            'name'  => $this->lang->english, //TODO: get lang key by lang user selected
            'image' => null,
            'sub'   => null,
        ];
    }
}
