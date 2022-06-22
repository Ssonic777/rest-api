<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\HidePost;
use App\Repositories\Base\BaseModelRepository;

/**
 * class HidePostRepository
 * @package App\Repositories
 */
class HidePostRepository extends BaseModelRepository
{
    /**
     * @return string
     */
    public function getModel(): string
    {
        return HidePost::class;
    }

    /**
     * @param int $userId
     * @param int $postId
     * @return bool
     */
    public function exists(int $userId, int $postId): bool
    {
        return $this->getModelClone()->newQuery()
                    ->where([
                        ['user_id', '=', $userId],
                        ['post_id', '=', $postId]
                    ])
                    ->exists();
    }
}
