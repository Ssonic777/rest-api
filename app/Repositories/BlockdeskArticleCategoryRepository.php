<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\BlockdeskArticleCategory;
use App\Repositories\Base\BaseModelRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class BlockdeskArticleCategoryRepository
 * @package App\Repositories
 */
class BlockdeskArticleCategoryRepository extends BaseModelRepository
{
    /**
     * @return string
     */
    protected function getModel(): string
    {
        return BlockdeskArticleCategory::class;
    }

    /**
     * @return Collection
     */
    public function getBlockdeskArticleCategories(): Collection
    {
        return $this->getModelClone()->newQuery()
                                    ->with(['lang' => function (HasOne $query): void {
                                        $query->select('*')
                                            ->where('type', '=', 'category');
                                    }])->get();
    }
}
