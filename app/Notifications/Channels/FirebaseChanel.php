<?php

declare(strict_types=1);

namespace App\Notifications\Channels;

use App\Models\User;
use App\Notifications\Contracts\BaseFCMNotification;
use App\Notifications\Platforms\CrossPlatform;
use App\Repositories\FCMTokenRepository;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging;

/**
 * class Firebase
 * @package App\Notifications\Channels
 */
class FirebaseChanel
{

    /**
     * @var string $nameNotificationDataMethod
     */
    private string $nameNotificationDataMethod = 'FCM';

    /**
     * @var CrossPlatform $crossPlatform
     */
    private CrossPlatform $crossPlatform;

    /**
     * @var Messaging $messaging
     */
    private Messaging $messaging;

    public function __construct(CrossPlatform $crossPlatform)
    {
        $this->messaging = app()->make('firebase.messaging');
        $this->crossPlatform = $crossPlatform;
    }

    /**
     * @param User $notifiable
     * @param Notification|BaseFCMNotification $notification
     * @throws \Kreait\Firebase\Exception\FirebaseException
     * @throws \Kreait\Firebase\Exception\MessagingException
     */
    public function send(User $notifiable, Notification $notification): void
    {
        $this->projectFCMSend($notifiable, $notification);
    }

    /**
     * @param User $user
     * @param BaseFCMNotification $FCMNotification
     * @throws \Kreait\Firebase\Exception\FirebaseException
     * @throws \Kreait\Firebase\Exception\MessagingException
     */
    private function projectFCMSend(User $user, BaseFCMNotification $FCMNotification): void
    {
        $notifiableDeviceTokens = $user->routeNotificationFor($this->nameNotificationDataMethod);

        if (count($notifiableDeviceTokens)) {
            $cloudMessage = $this->cloudMessage($user, $FCMNotification);
            $report = $this->messaging->sendMulticast($cloudMessage, $notifiableDeviceTokens);
            $this->failure($report);
            $this->deleteFailedTokens($user, $report);
        }
    }

    /**
     * @param Messaging\MulticastSendReport $report
     */
    private function failure(Messaging\MulticastSendReport $report): void
    {
        if ($report->hasFailures() && config('app.env') == 'local') {
            foreach ($report->failures()->getItems() as $failure) {
                echo $failure->error()->getMessage() . PHP_EOL;
            }
        }
    }

    /**
     * @param User $user
     * @param BaseFCMNotification|CrossPlatform $FCMNotification
     * @return Messaging\CloudMessage
     */
    private function cloudMessage(User $user, BaseFCMNotification $FCMNotification): Messaging\CloudMessage
    {
        $type = !is_null($FCMNotification::TYPE) ? $FCMNotification::TYPE : $FCMNotification::$type;

        return $this->crossPlatform->makeFCMNotification(
            $FCMNotification->toFCM($user),
            $type,
            $FCMNotification->FCMConfig()
        );
    }

    /**
     * @param User $user
     * @param Messaging\MulticastSendReport $multicastSendReport
     */
    private function deleteFailedTokens(User $user, Messaging\MulticastSendReport $multicastSendReport): void
    {
        $filedTokens = array_merge($multicastSendReport->invalidTokens(), $multicastSendReport->unknownTokens());

        if (count($filedTokens)) {
            resolve(FCMTokenRepository::class)->deleteDeviceTokens($user->user_id, $filedTokens);
        }
    }
}
