<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\GroupCategoryRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class GroupCategoryService
 * @package App\Services
 */
class GroupCategoryService
{
    /**
    * @var GroupCategoryRepository $repository
    */
    public GroupCategoryRepository $repository;

    /**
     * @param GroupCategoryRepository $repository
     */
    public function __construct(GroupCategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return Collection
     */
    public function getGroupCategories(): Collection
    {
        return $this->repository->getGroupCategories();
    }
}
