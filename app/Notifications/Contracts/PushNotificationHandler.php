<?php

declare(strict_types=1);

namespace App\Notifications\Contracts;

use App\Models\Notification;
use Closure;

/**
 * class PushNotificationHandler
 * @package App\Notifications\Contracts
 */
abstract class PushNotificationHandler extends \Illuminate\Notifications\Notification
{

    public const TYPE = null;

    /**
     * @var Notification $notification
     */
    protected Notification $notification;

    /**
     * @var int|null $countUnreadNotifications
     */
    protected ?int $countUnreadNotifications;

    /**
     * @var int|null $countUnreadNotificationsByType
     */
    private ?int $countUnreadNotificationsByType;

    /**
     * @param Notification $notification
     * @param Closure $next
     * @return Notification
     * @throws \Exception
     */
    public function handle(Notification $notification, Closure $next): Notification
    {
        $this->notification = $notification;

        if ($this->canExecute()) {
            $this->countUnreadNotifications = $notification->recipient->unreadNotifications()->count();
            $this->countUnreadNotificationsByType = $notification->recipient->unreadNotifications()->where('type', $this::TYPE)->count();
            $notification->recipient->notify($this);
        }

        return $next($notification);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    private function canExecute(): bool
    {
        if (is_null($this::TYPE)) {
            throw new \Exception('Type mus\'t be null');
        }

        return $this->notification->type == $this::TYPE;
    }

    /**
     * @return int
     */
    public function countUnreadNotificationsByType(): int
    {
        return $this->countUnreadNotificationsByType > 0 ? $this->countUnreadNotificationsByType - 1 : $this->countUnreadNotificationsByType;
    }
}
