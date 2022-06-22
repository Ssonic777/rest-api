<?php

namespace App\Traits\Model;

use Carbon\Carbon;

trait TimeAttributeConverter
{
    /**
     * @return string
     */
    public function getTimeAttribute(): string
    {
        $time = $this->getRawOriginal('time');

        return $time == 0 ?: Carbon::createFromTimestamp($time)->timestamp;
    }

    /**
     * @return string
     */
    public function getTimeDiffAttribute(): string
    {
        $time = $this->getRawOriginal('time');

        return $time == 0 ?: Carbon::createFromTimestamp($time)->diffForHumans();
    }
}
