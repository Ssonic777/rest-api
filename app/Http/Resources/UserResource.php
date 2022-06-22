<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\User;
use App\Traits\AutoloadAttributeJsonResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * class UserResource
 * @package App\Http\Resources
 * @property User $resource
 */
class UserResource extends JsonResource
{
    use AutoloadAttributeJsonResource;

    public const MODIFY_ATTRIBUTES = [
        'location' => 'last_location_update'
    ];

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
            'phone_number_code' => $this->when($this->phone_number, $this->phone_number_code),
            'country' => CountryResource::make($this->whenLoaded('country')),
            'field' => UserFieldResource::make($this->whenLoaded('field'))
        ];
    }
}
