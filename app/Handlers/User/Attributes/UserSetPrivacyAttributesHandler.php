<?php

declare(strict_types=1);

namespace App\Handlers\User\Attributes;

use App\Models\User;

/**
 * class UserSetPrivacyAttributes
 * @package App\Handlers\User\Attributes
 */
class UserSetPrivacyAttributesHandler
{
    /**
     * @param User $user
     * @return User
     */
    public static function execute(User $user): User
    {
        foreach (User::PRIVACY_FIELDS as $field => $statuses) {
            $user->setAttribute($field, array_search($user->getAttribute($field), $statuses));
        }

        return $user;
    }
}
