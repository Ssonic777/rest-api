<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\PostRepository;
use App\Repositories\HashtagRepository;
use App\ProjectClass\ProjectCursorPaginator;
use App\Models\Hashtag;

/**
 * class HotTodayTagPostService
 * @package App\Services
 */
class HotTodayTagPostService
{
    public PostRepository $postRepository;

    public HashtagRepository $hashtagRepository;

    /**
     * @param PostRepository $postRepository
     * @param HashtagRepository $hashtagRepository
     */
    public function __construct(PostRepository $postRepository, HashtagRepository $hashtagRepository)
    {
        $this->postRepository = $postRepository;
        $this->hashtagRepository = $hashtagRepository;
    }

    /**
     * @param string|null $tag
     * @param string $sorting
     * @param int $perPage
     * @return ProjectCursorPaginator
     */
    public function getHotTodayTagsPosts(User $user, ?string $tag, string $sorting, int $perPage): ProjectCursorPaginator
    {
        if (!empty($tag)) {
            $hotTodayTags = $this->hashtagRepository->getHashtagsByNames([$tag]);
        } else {
            $hotTodayTags = $this->getHotTodayTags();
        }

        $hotTodayTags = $hotTodayTags->pluck('id')->toArray();
        $hotTodayTagsPosts = $this->postRepository->getPostsWithTags($hotTodayTags, $sorting, $perPage);
        $hotTodayTagsPosts->setIsReportedAttributes($user->user_id);

        return $hotTodayTagsPosts;
    }

    /**
     * @return Collection
     */
    public function getHotTodayTags(): Collection
    {
        return $this->hashtagRepository->getHotTodayTags();
    }
}
