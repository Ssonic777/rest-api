<?php

declare(strict_types=1);

namespace App\Repositories;

use App\ProjectClass\ProjectCursorPaginator;
use App\Repositories\Base\BaseModelRepository;
use App\Models\Blog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;

/**
 * class BlogRepository
 * @package App\Repositories
 */
class BlogRepository extends BaseModelRepository
{
    protected const DEFAULT_FIELDS = [
        'id',
        'user',
        'title',
        'json_content',
        'description',
        'category',
        'thumbnail',
        'posted',
        'view',
        'tags'
    ];

    protected const BLOCK_FIELDS = [
        'id',
        'user',
        'title',
        'category',
        'thumbnail',
        'posted',
        'view',
    ];

    protected const TAB_FIELDS = [
        'id',
        'user',
        'title',
        'category',
        'thumbnail',
        'posted',
    ];

    /**
     * @return string
     */
    protected function getModel(): string
    {
        return Blog::class;
    }

    /**
     * @param int $limit
     * @return Collection
     */
    public function getTrendingBlockArticles(int $limit = 4): Collection
    {
        return $this->getModelClone()->newQuery()
            ->select(self::BLOCK_FIELDS)
            ->where('active', Blog::ACTIVE_STATUS)
            ->with('owner:user_id,first_name,last_name,avatar')
            ->with('catry.title')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    /**
     * @return Collection
     */
    public function getPopularBlockArticles(): Collection
    {
        $twoWeeksBeforeTimestamp = now()->subDays(5)->getTimestamp();

        return $this->getModelClone()->newQuery()
            ->select(self::BLOCK_FIELDS)
            ->where('posted', '>=', $twoWeeksBeforeTimestamp)
            ->where('active', '1')
            ->with('owner:user_id,first_name,last_name,avatar')
            ->with('catry.title')
            ->orderByDesc('view')
            ->limit(3)
            ->get();
    }

    /**
     * @return Collection
     */
    public function getEditorsChoiceBlockArticles(): Collection
    {
        return $this->getModelClone()->newQuery()
            ->select(self::BLOCK_FIELDS)
            ->where('editors_choice', 1)
            ->where('active', '1')
            ->with('owner:user_id,first_name,last_name,avatar')
            ->with('catry.title')
            ->orderByDesc('posted')
            ->limit(3)
            ->get();
    }

    /**
     * @param array $filters
     * @return ProjectCursorPaginator
     */
    public function getLatestBlockArticles(array $filters): ProjectCursorPaginator
    {
        $query = $this->getModelClone()->newQuery()
            ->select(self::BLOCK_FIELDS);

        $trendingBlockArticles = $this->getTrendingBlockArticles(4);
        $articlesToExclude = $trendingBlockArticles->pluck('id');

        if (isset($filters['categories_ids']) && !empty($filters['categories_ids'])) {
            $categoriesIds = explode(',', $filters['categories_ids']);

            $query->whereIn('category', $categoriesIds);
        }

        if (isset($filters['per_page']) && !empty($filters['per_page'])) {
            $perPage = $filters['per_page'];
        } else {
            $perPage = 6;
        }

        return $query->where('active', '1')
            ->whereNotIn('id', $articlesToExclude)
            ->where('posted', '>', 0)
            ->with([
                'owner:user_id,first_name,last_name,avatar',
                'catry.title',
            ])
            ->orderByDesc('id')
            ->projectCursorPaginate($perPage);
    }

    /**
     * @param int $articleId
     * @return Blog
     */
    public function getArticle(int $articleId): Blog
    {
        return $this->getModelClone()->newQuery()
            ->select(self::DEFAULT_FIELDS)
            ->where('active', Blog::ACTIVE_STATUS)
            ->with('owner', function ($query): void {
                $query->select([
                    'user_id',
                    'first_name',
                    'last_name',
                    'birthday',
                    'avatar',
                    'address',
                    'admin',
                ])
                ->with('position')
                ->withCount('articles');
            })
            ->with('catry.title')
            ->with('post', function ($query): void {
                $query->withCount('comments');
                $query->withCount('reactions');
            })
            ->findOrFail($articleId);
    }

    /**
     * @param int|null $userId
     * @param int|null $perPage
     * @return ProjectCursorPaginator
     */
    public function getMyArticles(?int $userId, int $perPage = null): ProjectCursorPaginator
    {
        return $this->getModelClone()->newQuery()
            ->select(self::TAB_FIELDS)
            ->where('active', '1')
            ->where('user', $userId)
            ->with('owner:user_id,first_name,last_name,avatar')
            ->with('catry.title')
            ->projectCursorPaginate($perPage);
    }

    /**
     * @param string $search
     * @param array $data
     * @param int|null $perPage
     * @return ProjectCursorPaginator
     */
    public function search(string $search, array $data = [], int $perPage = null): ProjectCursorPaginator
    {
        $build = $this->getModelClone()->newQuery()
                                       ->select($this->getSelect())
                                       ->with($this->getWith());

        if (count($data['parents'])) {
            foreach ($data['parents'] as $childName => $childData) {
                $build->whereIn($childName, $childData['ids'], 'or');
            }
        }

        foreach ($data['columns'] as $column) {
            $build->where($column, 'LIKE', "%{$search}%", 'or');
        }

        if (count($data['parents'])) {
            array_map(
                function (string $column) use ($build): void {
                    $build->orderByDesc($column);
                },
                array_keys($data['parents'])
            );
        }

        return $build->projectCursorPaginate($perPage);
    }

    /**
     * @param array $followings
     * @param int      $perPage
     * @return ProjectCursorPaginator
     */
    public function getBlockdeskArticleFollowing(array $followings, int $perPage): ProjectCursorPaginator
    {
        return $this->getModelClone()
                    ->newQuery()
                    ->select(self::TAB_FIELDS)
                    ->with(['catry'])
                    ->with([
                        'owner:user_id,avatar,first_name,last_name',
                    ])
                    ->whereIn('user', $followings)
                    ->whereNull('deleted_at')
                    ->orderByDesc('id')
                    ->projectCursorPaginate($perPage);
    }
}
