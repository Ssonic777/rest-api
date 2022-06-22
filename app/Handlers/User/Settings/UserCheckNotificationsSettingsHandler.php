<?php

declare(strict_types=1);

namespace App\Handlers\User\Settings;

use App\Models\User;

/**
 * class UserCheckNotificationsSettingsHandler
 * @package App\Handlers\User\Settings
 */
class UserCheckNotificationsSettingsHandler
{
    /**
     * @param int $notifierId
     * @param User $recipient
     * @return bool
     */
    public static function execute(int $notifierId, User $recipient): bool
    {
        if ($recipient->notifications_allow == User::NOTIFICATIONS_ALLOW_NO) {
            return false;
        }

        if ($recipient->notifications_from == User::NOTIFICATIONS_ALLOW_FROM_ONLY_ME && $notifierId != $recipient->user_id) {
            return false;
        }

        if (
            $recipient->notifications_from == User::NOTIFICATIONS_ALLOW_FROM_PEOPLE_FOLLOW_ME
            && $notifierId != $recipient->user_id
            && !$recipient->followers->contains($notifierId)
        ) {
            return false;
        }

        return true;
    }
}
