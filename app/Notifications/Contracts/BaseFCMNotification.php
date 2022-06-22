<?php

declare(strict_types=1);

namespace App\Notifications\Contracts;

use App\Models\User;

abstract class BaseFCMNotification extends PushNotificationHandler
{
    /**
     * @param User $notifiable
     * @return array
     */
    abstract public function toFCM(User $notifiable): array;

    /**
     * @return array
     */
    public function FCMConfig(): array
    {
        return [
            // 'sound' => 'default',
            // 'group' => 'api-dev-group',
            // 'badge' => 38
        ];
    }
}
