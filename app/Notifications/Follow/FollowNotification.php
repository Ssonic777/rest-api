<?php

namespace App\Notifications\Follow;

use App\Models\Follower;
use App\Models\User;
use App\Notifications\Contracts\BaseFCMNotification;
use App\Repositories\FollowerRepository;
use Illuminate\Bus\Queueable;

/**
 * class FollowNotification
 */
class FollowNotification extends BaseFCMNotification
{
    use Queueable;

    public const TYPE = 'following';

    /** @var FollowerRepository $followerRepository */
    private FollowerRepository $followerRepository;

    public function __construct(FollowerRepository $followerRepository)
    {
        $this->followerRepository = $followerRepository;
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

    public function toFCM(User $notifiable): array
    {
        /** @var Follower $follower */
        $follower = $this->followerRepository->findWhereOrNull([
            ['following_id', '=', $this->notification->notifier->user_id],
            ['follower_id', '=', $this->notification->recipient->user_id],
        ]);

        return [
            'title' => ($this->countUnreadNotificationsByType()) ? "{$this->notification->notifier->full_name} & {$this->countUnreadNotificationsByType()} other" : $this->notification->notifier->full_name,
            'subtitle' => Follower::TEXT_FOLLOW_STATUSES[$follower->active],
            'avatar' => $this->notification->notifier->avatar,
        ];
    }
}
