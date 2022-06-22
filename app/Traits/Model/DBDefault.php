<?php

namespace App\Traits\Model;

use Illuminate\Support\Str;

/**
 * class \Illuminate\Database\Eloquent\Model
 */
trait DBDefault
{
    /**
     * @param string $haystack
     * @param string $attribute
     * @return string|null
     */
    private function checkDBDefaultValue(string $haystack, string $attribute): ?string
    {
        $result = null;

        if (!Str::contains($haystack, $url = trim($this->getRawOriginal($attribute)))) {
            $result = sprintf("%s/{$url}", getenv('AWS_CDN'));
        }

        return $result;
    }
}
