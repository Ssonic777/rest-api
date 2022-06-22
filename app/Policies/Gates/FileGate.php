<?php

declare(strict_types=1);

namespace App\Policies\Gates;

use App\Models\User;

class FileGate
{
    /**
     * @param User $user
     * @param int $ownerId
     * @return bool
     */
    public function showFile(User $user, int $ownerId): bool
    {
        return $ownerId == $user->user_id;
    }

    /**
     * @param User $user
     * @param int $ownerId
     * @return bool
     */
    public function updateFile(User $user, int $ownerId): bool
    {
        return $ownerId == $user->user_id;
    }


    /**
     * @param User $user
     * @param int $ownerId
     * @return bool
     */
    public function deleteFile(User $user, int $ownerId): bool
    {
        return $ownerId == $user->user_id;
    }
}
