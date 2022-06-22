<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Repositories\PostRepository;
use App\Repositories\AdminConfigurationRepository;
use App\Repositories\HashtagRepository;
use App\ProjectClass\ProjectCursorPaginator;

/**
 * class TopTagPostService
 * @package App\Services
 */
class TopTagPostService
{
    public PostRepository $postRepository;
    public AdminConfigurationRepository $adminConfigurationRepository;
    public HashtagRepository $hashtagRepository;

    /**
     * @param PostRepository $postRepository
     * @param AdminConfigurationRepository $adminConfigurationRepository
     * @param HashtagRepository $hashtagRepository
     */
    public function __construct(
        PostRepository $postRepository,
        AdminConfigurationRepository $adminConfigurationRepository,
        HashtagRepository $hashtagRepository
    ) {
        $this->postRepository = $postRepository;
        $this->adminConfigurationRepository = $adminConfigurationRepository;
        $this->hashtagRepository = $hashtagRepository;
    }

    /**
     * @param User $user
     * @param string|null $tag
     * @param string $sorting
     * @param int $perPage
     * @return ProjectCursorPaginator
     */
    public function getTopTagsPosts(User $user, ?string $tag, string $sorting, int $perPage): ProjectCursorPaginator
    {
        if (!empty($tag)) {
            $topTags = [$tag];
        } else {
            $topTags = $this->getTopTags();
        }

        $topTags = $this->hashtagRepository->getHashtagsByNames($topTags)->pluck('id')->toArray();
        $topTagsPosts = $this->postRepository->getPostsWithTags($topTags, $sorting, $perPage);
        $topTagsPosts->setIsReportedAttributes($user->user_id);

        return $topTagsPosts;
    }

    /**
     * @return array
     */
    public function getTopTags(): array
    {
        return $this->adminConfigurationRepository->getTopTags();
    }
}
