<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\GroupCategory;
use App\Repositories\Base\BaseModelRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class GroupCategoryRepository
 * @package App\Repositories
 */
class GroupCategoryRepository extends BaseModelRepository
{

    /**
     * @return string
     */
    protected function getModel(): string
    {
        return GroupCategory::class;
    }

    /**
     * @return Collection
     */
    public function getGroupCategories(): Collection
    {
        return $this->getModelClone()->newQuery()->with('lang')->get();
    }
}
