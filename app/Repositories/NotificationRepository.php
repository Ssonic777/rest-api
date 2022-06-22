<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Notification;
use App\Repositories\Base\BaseModelRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * class NotificationRepository
 * @package App\Repositories
 */
class NotificationRepository extends BaseModelRepository
{
    const DEFAULT_WITH = ['post', 'comment', 'reply', 'notifier'];

    protected function getModel(): string
    {
        return Notification::class;
    }

    /**
     * This method for initialize default values ModelRepository
     */
    protected function initializeDefaultData(): void
    {
        $this->setWith(self::DEFAULT_WITH);
    }

    /**
     * @param int $userId
     * @return Collection
     */
    public function notifications(int $userId): Collection
    {
        return $this->getModelClone()->newQuery()->where('recipient_id', $userId)
                                                ->with($this->getWith())
                                                ->orderByDesc('id')
                                                ->get();
    }

    /**
     * @param array $data
     * @return $this
     */
    public function updateOrCreate(array $data): self
    {
        return $this->getModelClone()->newQuery()->updateOrCreate($data, $data);
    }
}
