<?php

namespace App\Traits\Model;

use Carbon\Carbon;

trait LastSeenAttributeConverter
{
    /**
     * @return string
     */
    public function getLastseenAttribute(): string
    {
        $this->attributes['lastseen'] = Carbon::createFromTimestamp($this->attributes['lastseen'])
            ->isoFormat('dddd, MMMM Do YYYY, h:mm');

        return $this->attributes['lastseen'];
    }
}
