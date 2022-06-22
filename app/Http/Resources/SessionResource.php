<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Traits\AutoloadAttributeJsonResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * class SessionResource
 * @package \App\Http\Resources
 *
 * @property Model $resource
 */
class SessionResource extends JsonResource
{
    use AutoloadAttributeJsonResource;

    public const MODIFY_ATTRIBUTES = [
        'expire_at' => 'expire'
    ];

    /**
     * @return string[]
     */
    protected function makeModifyAttributes(): array
    {
        return self::MODIFY_ATTRIBUTES;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array
    {
        return $this->parse();
    }
}
