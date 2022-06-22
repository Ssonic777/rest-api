<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use App\Notifications\Contracts\BaseFCMNotification;
use Illuminate\Bus\Queueable;

/**
 * class CrossPlatformNotification
 * @package App\Notifications
 */
class CrossPlatformNotification extends BaseFCMNotification
{
    use Queueable;

    public static ?string $type = null;

    /**
     * @var array $data
     */
    private array $data = [

    ];

    /**
     * @param array $data
     * @param string $type
     */
    public function __construct(array $data, string $type)
    {
        self::$type = $type;
        $this->data = array_merge($this->data, $data);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['firebase'];
    }

    /**
     * @param User $notifiable
     * @return array
     */
    public function toFCM(User $notifiable): array
    {
        return $this->data;
    }
}
