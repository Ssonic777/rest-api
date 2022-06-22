<?php

declare(strict_types=1);

namespace App\Services;

use App\Collections\PostCollection;
use App\Repositories\PostRepository;
use App\Services\ServiceHandlers\PostServiceHandler;
use App\Models\User;
use App\ProjectClass\ProjectCursorPaginator;
use App\Models\Post;

/**
 * Class UserTimelineSearchService
 * @package App\Services
 */
class UserTimelineSearchService
{
    public PostRepository $postRepository;

    private PostServiceHandler $postHandler;

    /**
     * @param PostRepository $postRepository
     * @param PostServiceHandler $postHandler
     */
    public function __construct(PostRepository $postRepository, PostServiceHandler $postHandler)
    {
        $this->postRepository = $postRepository;
        $this->postHandler = $postHandler;
    }

    /**
     * @param User $authUser
     * @param int $userId
     * @param string $search
     * @param int $perPage
     * @return ProjectCursorPaginator
     */
    public function searchUserTimeline(User $authUser, int $userId, string $search, int $perPage): ProjectCursorPaginator
    {
        $ignorePostIds = $authUser->hidePosts->pluck('post_id')->toArray();
        $pinnedPostsIds = $authUser->pinnedPosts->pluck('post_id')->toArray();
        $ignorePostIds = array_merge($ignorePostIds, $pinnedPostsIds);
        /** @var ProjectCursorPaginator|PostCollection $foundPosts */
        $foundPosts = $this->postRepository->searchUserTimeline($userId, $ignorePostIds, $search, $perPage);
        $foundPosts->each(fn (Post $post) => $this->postHandler->setAttributes($post));
        $foundPosts->setIsLikedAttributes($authUser->user_id);
        $foundPosts->setIsReportedAttributes($authUser->user_id);

        return $foundPosts;
    }
}
