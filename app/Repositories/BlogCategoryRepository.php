<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\BlogCategory;
use App\Repositories\Base\BaseModelRepository;

/**
 * class BlogCategoryRepository
 * @package App\Repositories
 */
class BlogCategoryRepository extends BaseModelRepository
{
    protected function getModel(): string
    {
        return BlogCategory::class;
    }
}
