<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BlockdeskArticleCategory;
use App\Repositories\BlockdeskArticleCategoryRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class ArticleCategoryService
 * @package App\Services
 */
class BlockdeskArticleCategoryService
{
    /**
    * @var BlockdeskArticleCategoryRepository $repository
    */
    public BlockdeskArticleCategoryRepository $repository;

    /**
     * @param BlockdeskArticleCategoryRepository $repository
     */
    public function __construct(BlockdeskArticleCategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return Collection
     */
    public function getBlockdeskArticleCategories(): Collection
    {
        return $this->repository->getBlockdeskArticleCategories()->where('lang', '!=', null);
    }
}
