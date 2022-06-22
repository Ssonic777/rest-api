<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\GroupAdditionalData;
use App\Repositories\Base\BaseModelRepository;

/**
 * class GroupAdditionalDataRepository
 * @package App\Repositories
 */
class GroupAdditionalDataRepository extends BaseModelRepository
{

    public function getModel(): string
    {
        return GroupAdditionalData::class;
    }
}
