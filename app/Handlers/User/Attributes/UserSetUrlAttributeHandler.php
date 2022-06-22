<?php

declare(strict_types=1);

namespace App\Handlers\User\Attributes;

use App\Models\User;

/**
 * class SetUrlAttributeHandler
 * @package App\Handlers\User\Attributes
 */
class UserSetUrlAttributeHandler
{
    /**
     * @param User $user
     * @return User
     */
    public static function execute(User $user): User
    {
        return $user->setAttribute('url', getenv('SITE_URL') . '/' . $user->username);
    }
}
