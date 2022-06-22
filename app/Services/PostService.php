<?php

declare(strict_types=1);

namespace App\Services;

use App\Collections\PostCollection;
use App\Handlers\User\Attributes\UserSetIsReportedAttribute;
use App\Models\Hashtag;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\Report;
use App\Models\User;
use App\ProjectClass\ProjectCursorPaginator;
use App\Repositories\HashtagRepository;
use App\Repositories\PostRepository;
use App\Repositories\Redis\FileRepository;
use App\Repositories\ReportRepository;
use App\Repositories\UserRepository;
use App\Services\ServiceHandlers\PostServiceHandler;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\ConfigRepository;
use App\Models\PinnedPost;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * Class PostService
 * @package App\Services
 */
class PostService
{
    public const CLEAR_STRING = '/^[a-zA-Z0-9_!\-\–:\. ]+$/';

    /**
     * @var PostRepository $repository
     */
    public PostRepository $repository;

    /**
     * @var ConfigRepository $configRepository
     */
    public ConfigRepository $configRepository;

    /**
     * @var ConfigService $configService
     */
    public ConfigService $configService;

    /**
     * @var UserRepository $userRepository
     */
    private UserRepository $userRepository;

    /**
     * @var PostServiceHandler $handler
     */
    private PostServiceHandler $handler;

    private HashtagRepository $hashtagRepository;

    public function __construct(
        PostRepository $repository,
        ConfigRepository $configRepository,
        FileRepository $fileRepository,
        ConfigService $configService,
        UserRepository $userRepository,
        PostServiceHandler $handler,
        HashtagRepository $hashtagRepository
    ) {
        $this->repository = $repository;
        $this->configRepository = $configRepository;
        $this->fileRepository = $fileRepository;
        $this->configService = $configService;
        $this->userRepository = $userRepository;
        $this->handler = $handler;
        $this->hashtagRepository = $hashtagRepository;
    }

    /**
     * @param User $user
     * @param int|null $perPage
     * @return ProjectCursorPaginator
     */
    public function getUserPosts(User $user, int $perPage = null): ProjectCursorPaginator
    {
        /** @var ProjectCursorPaginator $posts */
        $posts = $this->repository->getUserPosts($user->user_id, $perPage);
        $posts->each(fn (Post $post) => $this->handler->setAttributes($post));
        $posts->setIsReportedAttributes($user->user_id);

        return $posts;
    }

    /**
     * @param User $user
     * @param array $data
     * @return Post
     */
    public function storePost(User $user, array $data): Post
    {
        if (!empty($data['attachments']) && !empty($data['service_gif'])) {
            throw new BadRequestException('Only attachments or service_gif');
        }

        $data = $this->handler->parseModelAttributes($data);

        if (isset($data['recipient_id'])) {
            /** @var User $recipient */
            $recipient = $this->userRepository->getUser((int) $data['recipient_id']);

            if (
                $recipient->post_privacy == User::PRIVACY_POST_TIMELINE_NOBODY ||
                (
                    $recipient->post_privacy == User::PRIVACY_POST_TIMELINE_PEOPLE_I_FOLLOW &&
                    !$recipient->followings->contains('user_id', $user->user_id)
                )
            ) {
                throw new BadRequestException('Posting to this recepient\'s timeline is forbidden by his privacy settings.');
            }
        }

        /** @var Post $createdPost */
        $createdPost = $user->posts()->create($data);
        $this->handler->uploadAttachments($createdPost, $data);
        $this->updatePostHashtagsRelations($createdPost);

        /** @var Post $createdPost */
        $createdPost = $this->repository->find($createdPost->id);
        $this->handler->setAttributes($createdPost);
        UserSetIsReportedAttribute::execute($createdPost, $user->user_id);

        return $createdPost;
    }

    /**
     * @param User $user
     * @param int $postId
     * @param array $data
     * @return Post
     */
    public function updatePost(User $user, int $postId, array $data): Post
    {
        /** @var Post $foundPost */
        $foundPost = $this->repository->find($postId, ['*']);
        $foundPost->update($this->handler->parseModelAttributes($data));
        $this->handler->updateAttachments($foundPost, $data);
        $this->updatePostHashtagsRelations($foundPost);
        /** @var Post $foundPost */
        $foundPost = $this->repository->find($postId, PostRepository::DEFAULT_SELECT);
        $this->handler->setAttributes($foundPost);
        UserSetIsReportedAttribute::execute($foundPost, $user->user_id);

        return $foundPost;
    }

    /**
     * @param int $postId
     */
    public function deletePost(int $postId): void
    {
        /** @var Post $foundPost */
        $foundPost = $this->repository->find($postId, ['*']);
        $this->handler->deletingMedias($foundPost);
        $foundPost->delete();
    }

    /**
     * @param User $fromUser
     * @param int $postId
     * @return array
     */
    public function likePostToggle(User $fromUser, int $postId): array
    {
        /** @var Post $foundPost */
        $foundPost = $this->repository->find($postId);
        $doesntLiked = $foundPost->reactions()->wherePivot('user_id', $fromUser->user_id)->doesntExist();

        if ($doesntLiked) {
            $fromUser->postReactions()->attach(['post_id' => $postId], ['reaction' => Reaction::LOVE]);
            $status = 'Liked';
        } else {
            $fromUser->postReactions()->detach(['post_id' => $postId]);
            $status = 'Unliked';
        }

        return [
            'message' => $status,
            'likes_count' => $foundPost->reactions()->count(),
        ];
    }

    /**
     * @param int $postId
     * @return Collection
     */
    public function getLikedUsers(int $postId): Collection
    {
        /** @var Post $foundPost */
        $foundPost = $this->repository->find($postId);

        return $foundPost->likes()->select('username', 'first_name', 'last_name', 'avatar')->get();
    }

    /**
     * @param User $user
     * @param int|null $perPage
     * @return ProjectCursorPaginator
     */
    public function getFeed(User $user, ?int $perPage): ProjectCursorPaginator
    {
        /** @var Collection $userDistinctReportedPosts */
        $userDistinctReportedPosts = app()->call(ReportRepository::class . '@getReports', [
                                                                                'userId' => $user->user_id,
                                                                                'type' => 'post',
                                                                                'unique' => true
                                                                            ]);

        /** @var Collection $blockedPosts */
        $blockedPosts = $userDistinctReportedPosts->map(
            fn(Report $report) => $report->post
        );

        /** @var ProjectCursorPaginator|PostCollection $feeds */
        $feeds = $this->repository->getFeed($user->user_id, $blockedPosts, $perPage);
        $feeds->each(fn (Post $post) => $this->handler->setAttributes($post));

        $feeds->setIsLikedAttributes($user->user_id);
        $feeds->setIsPinnedAttributes();
        $feeds->setIsReportedAttributes($user->user_id);

        return $feeds;
    }

    /**
     * @param User $user
     * @param string $search
     * @param int|null $perPage
     * @return ProjectCursorPaginator
     */
    public function searchFeed(User $user, string $search, ?int $perPage): ProjectCursorPaginator
    {
        $blockedPostsIds = $this->getBlockedReportedPosts($user->user_id);

        /** @var ProjectCursorPaginator|PostCollection $feeds */
        $feeds = $this->repository->getFeedSearch($user->user_id, $blockedPostsIds, $search, $perPage);

        $feeds->each(fn (Post $post) => $this->handler->setAttributes($post));
        $feeds->setIsLikedAttributes($user->user_id);
        $feeds->setIsPinnedAttributes();
        $feeds->setIsReportedAttributes($user->user_id);

        return $feeds;
    }

    /**
     * @param int $user_id
     * @return array
     */
    public function getBlockedReportedPosts(int $user_id): array
    {
        /** @var Collection $userDistinctReportedPosts */
        $userDistinctReportedPosts = app()->call(ReportRepository::class . '@getReports', [
            'userId' => $user_id,
            'type' => 'post',
            'unique' => true
        ]);

        /** @var Collection $blockedPosts */
        $blockedPosts = $userDistinctReportedPosts->map(
            fn(Report $report) => $report->post
        );

        return array_filter($blockedPosts->pluck('post_id')->toArray());
    }

    /**
     * @param int $userId
     * @param int|null $perPage
     * @return ProjectCursorPaginator
     */
    public function userPosts(int $userId, ?int $perPage): ProjectCursorPaginator
    {
        /** @var ProjectCursorPaginator|PostCollection $feeds */
        $feeds = $this->repository->getFeed($userId, collect(), $perPage);
        $feeds->setIsLikedAttributes($userId);
        $feeds->setIsPinnedAttributes();

        return $feeds;
    }

    /**
     * @param User $authUser
     * @param int $userId
     * @param int|null $perPage
     * @return ProjectCursorPaginator
     */
    public function userPostsSimple(User $authUser, int $userId, ?int $perPage): ProjectCursorPaginator
    {
        /** @var ProjectCursorPaginator|PostCollection $feeds */
        $feeds = $this->repository->getFeedSimple($userId, collect(), $perPage);
        $feeds->each(fn (Post $post) => $this->handler->setAttributes($post));

        $feeds->setIsLikedAttributes($authUser->user_id);
        $feeds->setIsPinnedAttributes();

        return $feeds;
    }

    /**
     * @param User $user
     * @param int $postId
     * @param array $fields
     * @return Post
     */
    public function showPost(User $user, int $postId, array $fields = []): Post
    {
        $foundPost = $this->repository->showPost($postId, $fields);
        $this->handler->setAttributes($foundPost);
        UserSetIsReportedAttribute::execute($foundPost, $user->user_id);

        return $foundPost;
    }

    /**
     * @param int $postId
     * @param int $userId
     * @return array
     */
    public function pinToggle(int $postId, int $userId): array
    {
        $post = $this->repository->getModelClone()
            ->newQuery()
            ->where('user_id', $userId)
            ->findOrFail($postId);

        if ($post->pin()->doesntExist()) {
            $post->pin()->create([
                'post_id' => $postId,
                'user_id' => $userId,
                'active' => PinnedPost::STATUS_ACTIVE,
            ]);
            $message = 'Pinned';
        } else {
            $post->pin()->delete();
            $message = 'Unpinned';
        }

        return [
            'message' => $message,
        ];
    }

    /**
     * @param User $authUser
     * @param int $userId
     * @param int $perPage
     * @return ProjectCursorPaginator
     */
    public function getUserTimeline(User $authUser, int $userId, int $perPage): ProjectCursorPaginator
    {
        $ignorePostIds = $authUser->hidePosts->pluck('post_id')->toArray();
        $pinnedPostsIds = $authUser->pinnedPosts->pluck('post_id')->toArray();
        $ignorePostIds = array_merge($ignorePostIds, $pinnedPostsIds);
        $timeline = $this->repository->getUserTimeline($userId, $ignorePostIds, $perPage);
        $timeline->each(fn (Post $post) => $this->handler->setAttributes($post));
        $timeline->setIsLikedAttributes($authUser->user_id);
        $timeline->setIsReportedAttributes($authUser->user_id);

        return $timeline;
    }

    /**
     * @param User $authUser
     * @param int $perPage
     * @return ProjectCursorPaginator
     */
    public function getUserTimelinePinned(User $authUser, int $perPage): ProjectCursorPaginator
    {
        $ignorePostIds = $authUser->hidePosts->pluck('post_id')->toArray();
        $pinnedPostsIds = $authUser->pinnedPosts->pluck('post_id')->toArray();
        $timelinePinned = $this->repository->getUserTimelinePinned($authUser->user_id, $pinnedPostsIds, $ignorePostIds, $perPage);
        $timelinePinned->each(fn (Post $post) => $this->handler->setAttributes($post));
        $timelinePinned->setIsLikedAttributes($authUser->user_id);
        $timelinePinned->setIsReportedAttributes($authUser->user_id);

        return $timelinePinned;
    }

    /**
     * @param Post $post
     */
    public function updatePostHashtagsRelations(Post $post): void
    {
        if (empty($post->postText)) {
            return;
        }

        preg_match_all(Hashtag::HASHTAG_REGEX, $post->postText, $matches);

        $hashtags = $this->hashtagRepository->getHashtagsByNames($matches[1]);
        $existingHashtags = $hashtags->pluck('tag')->toArray();

        foreach ($matches[1] as $hashtag) {
            if (!in_array($hashtag, $existingHashtags)) {
                $newHashtag = $this->hashtagRepository->create([
                    'hash' => md5($hashtag),
                    'tag' => $hashtag,
                ]);
                $hashtags->add($newHashtag);
            }
        }

        $post->hashtags()->sync($hashtags);
        $post->refresh();
    }
}
