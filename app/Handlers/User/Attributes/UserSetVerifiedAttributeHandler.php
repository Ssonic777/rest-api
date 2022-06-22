<?php

declare(strict_types=1);

namespace App\Handlers\User\Attributes;

use App\Models\User;

/**
 * class SetVerifiedAttributeHandler
 * @package App\Models\User
 */
class UserSetVerifiedAttributeHandler
{
    /**
     * @param User $user
     * @return User
     */
    public static function execute(User $user): User
    {
        $verified = $user->verified ? 'Verified' : 'Not verified';

        return $user->setAttribute('verified', $verified);
    }
}
