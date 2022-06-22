<?php

declare(strict_types=1);

namespace App\Collections;

use Illuminate\Database\Eloquent\Collection;

/**
 * Class MessageCollection
 * @package App\Collections
 */
class MessageCollection extends Collection
{

    /**
     * @param int $fromId
     * @return int
     */
    public function getCountUnReadMsgs(int $fromId): int
    {
        return $this->where('from_id', '=', $fromId)
                    ->where('seen', '=', '0')
                    ->count();
    }
}
