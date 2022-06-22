<?php

declare(strict_types=1);

namespace App\Http\Resources\Group;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * class GroupAdditionalDataResource
 * @package App\Http\Resources\Group
 */
class GroupAdditionalDataResource extends JsonResource
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
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'location' => $this->location,
            'facebook' => $this->facebook,
            'instagram' => $this->instagram,
            'twitter' => $this->twitter,
            'vkontakte' => $this->vkontakte,
            'youtube' => $this->youtube,
            'linkedin' => $this->linkedin
        ];
    }
}
