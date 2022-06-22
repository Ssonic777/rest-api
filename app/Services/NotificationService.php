<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Contracts\NotificationInterface;
use App\Models\FCMToken;
use App\Models\Notification;
use App\Models\User;
use App\Notifications\CrossPlatformNotification;
use App\Repositories\FCMTokenRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * class NotificationService
 * @package App\Services
 */
class NotificationService
{
    /**
     * @var NotificationRepository $repository
     */
    private NotificationRepository $repository;

    public function __construct(
        NotificationRepository $repository,
        FCMTokenRepository $FCMTokenRepository,
        UserRepository $userRepository
    ) {
        $this->repository = $repository;
        $this->FCMTokenRepository = $FCMTokenRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param int $userId
     * @param array $data
     * @return array|null
     * @throws \Exception
     */
    public function notifier(int $userId, array $data): ?array
    {
        /** @var User $foundUser */
        $foundUser = $this->userRepository->find($userId);
        unset($data['user_id']);

        if (!count($data)) {
            $testToken = Str::random(random_int(5, 10));
            $keys = ['title', 'subtitle', 'body', 'type', 'key'];

            foreach ($keys as $key => $val) {
                $data[$val] = sprintf('%s: %s', strtoupper($val), $testToken);
            }
        }

        if (!array_key_exists('type', $data)) {
            $data['type'] = 'default-type';
        }

        $type = $data['type'];
        unset($data['type']);
        $foundUser->notify(new CrossPlatformNotification($data, $type));

        $nativeData = app()->call('\App\Notifications\Platforms\CrossPlatform@toArray', [
            'data' => $data,
            'type' => $type,
            'config' => []
        ]);

        return $nativeData;
    }

    /**
     * @param User $user
     * @return Collection
     */
    public function getNotifications(User $user): Collection
    {
        return $this->repository->notifications($user->user_id);
    }

    /**
     * @param int $recipientId
     * @param array $data
     * @return Notification
     */
    public function storeById(int $recipientId, array $data): Notification
    {
        $authUserId = auth()->guard('api')->id();

        /** @var array $data */
        $data = array_merge($data, [
            'notifier_id' => $authUserId,
            'recipient_id' => $recipientId,
        ]);

        return $this->repository->updateOrCreate($data);
    }

    /**
     * @param int $notifierId
     * @param int $recipientId
     * @param string $type
     * @param array $data
     * @return Model|Notification|null
     */
    public function store(int $notifierId, int $recipientId, string $type, array $data): ?Notification
    {
        if (getenv('APP_ENV') != 'local' && $notifierId == $recipientId) {
            return null;
        }

        $data = array_merge([
            'notifier_id' => $notifierId,
            'recipient_id' => $recipientId,
            'type' => $type,
        ], $data);

        return $this->repository->getModelClone()->newQuery()->create($data);
    }

    /**
     * @param NotificationInterface $model
     */
    public function delete(NotificationInterface $model): void
    {
        /** @var Notification $foundNotification */
        $foundNotification = $model->notification;

        if ($foundNotification) {
            $foundNotification->delete();
        }
    }
    /**
     * @param User $user
     * @param array $validated
     * @return array
     */
    public function saveDeviceToken(User $user, array $validated): array
    {
        /** @var FCMToken $res */
        $fcmToken = $user->FCMTokens()->firstOrCreate($validated, $validated);

        return [
            'status' => $fcmToken instanceof FCMToken,
        ];
    }
}
