<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Repositories\Base\BaseModelRepository;
use App\Models\Hashtag;
use Illuminate\Database\Eloquent\Collection;

/**
 * class HashtagRepository
 * @package App\Repositories;
 */
class HashtagRepository extends BaseModelRepository
{
    /**
     * @return string
     */
    public function getModel(): string
    {
        return Hashtag::class;
    }

    /**
     * @return Collection
     */
    public function getHotTodayTags(): Collection
    {
        return $this->getModelClone()
            ->newQuery()
            ->distinct()
            ->leftJoin('v2_hashtag_popularities', 'Wo_Hashtags.id', '=', 'v2_hashtag_popularities.hashtag_id')
            ->where('v2_hashtag_popularities.created_at', '>', now()->subDay())
            ->withCount('popularityToday')
            ->orderByDesc('popularity_today_count')
            ->limit(20)
            ->get();
    }

    /**
     * @param array $listOfIds
     * @return Collection
     */
    public function getHashtagsByIds(array $listOfIds): Collection
    {
        return $this->getModelClone()
            ->newQuery()
            ->whereIn('id', $listOfIds)
            ->get();
    }

    /**
     * @param array $listOfNames
     * @return Collection
     */
    public function getHashtagsByNames(array $listOfNames): Collection
    {
        return $this->getModelClone()
            ->newQuery()
            ->whereIn('tag', $listOfNames)
            ->get();
    }
}
