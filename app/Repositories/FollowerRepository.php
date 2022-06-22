<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Follower;
use App\Repositories\Base\BaseModelRepository;

/**
 * class FollowerRepository
 * @package App\Repositories
 */
class FollowerRepository extends BaseModelRepository
{
    /**
     * @return string
     */
    public function getModel(): string
    {
        return Follower::class;
    }
}
