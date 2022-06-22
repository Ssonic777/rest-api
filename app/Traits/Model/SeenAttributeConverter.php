<?php

namespace App\Traits\Model;

use App\Models\Message;
use Carbon\Carbon;

trait SeenAttributeConverter
{
    /**
     * @return string
     */
    public function getSeenAttribute(): string
    {
        $seen = $this->attributes['seen'];
        return $seen == 0 ? Message::MESSAGE_NOT_SEEN : Carbon::createFromTimestamp($seen)->isoFormat('dddd, MMMM Do YYYY, h:mm');
    }

    /**
     * @return string|null
     */
    public function getSeenDiffAttribute(): string
    {
        $seen = $this->attributes['seen'];
        return $seen == 0 ? Message::MESSAGE_NOT_SEEN : Carbon::parse($seen)->diffForHumans();
    }
}
