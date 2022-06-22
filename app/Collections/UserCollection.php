<?php

declare(strict_types=1);

namespace App\Collections;

use App\Handlers\User\Attributes\UserSetIsFriendAttributeHandler;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * class UserCollection
 * @package App\Collection
 */
class UserCollection extends Collection
{
    /**
     * @param int $forUserId
     * @return void
     */
    public function setIsFriendAttribute(int $forUserId): self
    {
        $this->each(function (User $user) use ($forUserId): void {
            UserSetIsFriendAttributeHandler::execute($user, $forUserId);
        });

        return $this;
    }
}
