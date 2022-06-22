<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Traits\AutoloadAttributeJsonResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * class UserFieldResource
 * @package App\Http\Resources
 */
class UserFieldResource extends JsonResource
{
    use AutoloadAttributeJsonResource;

    public const MODIFY_ATTRIBUTES = [
        'position' => 'fid_1'
    ];

    /**
     * @return string[]
     */
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
}
