<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class BlockdeskArticleCategoryResource
 * @package App\Http\Resources
 */
class BlockdeskArticleCategoryResource extends JsonResource
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
        ];
    }
}
