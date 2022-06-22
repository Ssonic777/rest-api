<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Post;
use App\Models\User;
use App\ProjectClass\ProjectCursorPaginator;
use App\Repositories\Base\BaseModelRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PostRepository
 * @package App\Repositories
 * @property $model Post
 */
class PostRepository extends BaseModelRepository
{
    public const DEFAULT_SELECT = [
        'id',
        'post_id',
        'user_id',
        'postText',
        'postPrivacy',
        'postLink',
        'postFile',
        'time',
        'comments_status',
        'postSticker',
        'parent_id',
    ];

    public const DEFAULT_WITH = [
        'medias',
        'user:user_id,username,first_name,last_name,avatar',
        'parent:id,post_id,user_id,postText,postPrivacy,postLink,postFile,time,comments_status,postSticker',
        'parent.medias',
        'parent.user:user_id,username,first_name,last_name,avatar',
        'reports',
    ];

    public const DEFAULT_WITH_COUNT = [
        'reactions',
        'comments',
        'pin',
    ];

    protected function getModel(): string
    {
        return Post::class;
    }

    protected function initializeDefaultData(): void
    {
        $this->setSelect(self::DEFAULT_SELECT);
        $this->setWith(self::DEFAULT_WITH);
        $this->setWithCount(self::DEFAULT_WITH_COUNT);
    }

    /**
     * @param int $userId
     * @param int|null $perPage
     * @return ProjectCursorPaginator
     */
    public function getUserPosts(int $userId, int $perPage = null): ProjectCursorPaginator
    {
        return $this->getModelClone()->newQuery()
                                    ->select($this->getSelect())
                                    ->where('user_id', '=', $userId)
                                    ->with($this->getWith())
                                    ->withCount($this->getWithCount())
                                    ->projectCursorPaginate($perPage);
    }

    /**
     * @param int $postId
     * @param array $fields
     * @return Post
     */
    public function showPost(int $postId, array $fields = []): Post
    {
        return $this->getModelClone()
            ->newQuery()
            ->select(array_merge($this->getSelect(), $fields))
            ->with($this->getWith())
            ->withCount($this->getWithCount())
            ->findOrFail($postId);
    }

    /**
     * @return ProjectCursorPaginator
     */
    public function getPostsPaginate(): ProjectCursorPaginator
    {
        return $this->getModelClone()->newQuery()
                                    ->select($this->getSelect())
                                    ->projectCursorPaginate();
    }

    /**
     * @param int $userId
     * @param Collection $blockedPosts
     * @param int|null $perPage
     * @return ProjectCursorPaginator
     */
    public function getFeed(int $userId, Collection $blockedPosts, ?int $perPage): ProjectCursorPaginator
    {
        $blockedPostsIds = array_filter($blockedPosts->pluck('post_id')->toArray());

        return $this->getModelClone()->newQuery()
                    ->whereNotIn('id', $blockedPostsIds)
                    ->select($this->getSelect())
                    ->where('id', '>', 0)
                    ->where('postType', '<>', 'profile_picture_deleted')
                    ->where('user_id', '>', 0)
                    ->where('multi_image_post', '=', 0)
                    ->where(
                        function (Builder $query) use ($userId) {
                            $query->where('postPrivacy', '<>', '3')
                                ->orWhere(
                                    function (Builder $query) use ($userId) {
                                        $query->where('user_id', '=', $userId)
                                            ->where('postPrivacy', '>=', 0);
                                    }
                                );
                        }
                    )
                    ->whereNotIn('postShare', [1])
                    ->where('job_id', '=', 0)
                    ->whereNotIn(
                        'id',
                        function (\Illuminate\Database\Query\Builder $query) use ($userId): void {
                            $query->select('post_id')
                                ->from('Wo_HiddenPosts')
                                ->where('user_id', '=', $userId);
                        }
                    )
                    ->with($this->getWith())
                    ->withCount('reactions', 'comments')
                    ->orderBy('id', 'DESC')
                    ->projectCursorPaginate($perPage);
    }

    /**
     * @param int $userId
     * @param array $blockedPostsIds
     * @param string $search
     * @param int|null $perPage
     * @return ProjectCursorPaginator
     */
    public function getFeedSearch(int $userId, array $blockedPostsIds, string $search, ?int $perPage): ProjectCursorPaginator
    {

        $feeds =  $this->getModelClone()->newQuery()
                    ->whereNotIn('id', $blockedPostsIds)
                    ->select($this->getSelect())
                    ->where('id', '>', 0)
                    ->where('postType', '<>', 'profile_picture_deleted')
                    ->where('user_id', '>', 0)
                    ->where('multi_image_post', '=', 0)
                    ->where(
                        function (Builder $query) use ($search) {
                            $query->where('postText', 'like', '%' . $search . '%')
                                ->orWhere('postLinkTitle', 'like', '%' . $search . '%')
                                ->orWhereIn(
                                    'user_id',
                                    function (\Illuminate\Database\Query\Builder $query) use ($search): void {
                                        $query->select('user_id')
                                            ->from('Wo_Users')
                                            ->where('first_name', 'like', '%' . $search . '%')
                                            ->orWhere('last_name', 'like', '%' . $search . '%');
                                    }
                                );
                        }
                    )
                    ->where(
                        function (Builder $query) use ($userId, $search) {
                            $query->where('postPrivacy', '<>', '3')
                                ->orWhere(
                                    function (Builder $query) use ($userId, $search) {
                                        $query->where('user_id', '=', $userId)
                                            ->where('postPrivacy', '>=', 0);
                                    }
                                );
                        }
                    )
                    ->whereNotIn('postShare', [1])
                    ->where('job_id', '=', 0)
                    ->whereNotIn(
                        'id',
                        function (\Illuminate\Database\Query\Builder $query) use ($userId): void {
                            $query->select('post_id')
                                ->from('Wo_HiddenPosts')
                                ->where('user_id', '=', $userId);
                        }
                    )
                    ->with($this->getWith())
                    ->withCount('reactions', 'comments')
                    ->orderBy('id', 'DESC')
                    ->projectCursorPaginate($perPage);

        return $feeds;
    }

    /**
     * @param int $userId
     * @param Collection $blockedPosts
     * @param int|null $perPage
     * @return ProjectCursorPaginator
     */
    public function getFeedSimple(int $userId, Collection $blockedPosts, ?int $perPage): ProjectCursorPaginator
    {
        $blockedPostsIds = array_filter($blockedPosts->pluck('post_id')->toArray());

        return $this->getModelClone()->newQuery()
            ->where('user_id', $userId)
            ->whereNotIn('id', $blockedPostsIds)
            ->select('id', 'post_id', 'postText', 'postLink', 'postPhoto', 'time', 'user_id')
            ->with($this->getWith())
            ->withCount('reactions', 'comments')
            ->orderBy('id', 'desc')
            ->projectCursorPaginate($perPage);
    }

    /**
     * @param User $user
     * @param array $searchData
     * @param int|null $perPage
     * @return ProjectCursorPaginator
     */
    public function searchPosts(User $user, array $searchData, int $perPage = null): ProjectCursorPaginator
    {
        $followersIds = $user->followers()->pluck('user_id')->toArray();
        $followingsIds = $user->followings()->pluck('user_id')->toArray();

        $userIds = array_unique(array_merge($followersIds, $followingsIds, [$user->user_id]));

        return $this->getModelClone()->newQuery()
                    ->whereIn('user_id', $userIds)
                    ->whereHas('user', function (Builder $query) use ($searchData): void {
                        foreach (array_filter($searchData) as $key => $val) {
                            $query->where($key, 'LIKE', "{$val}%");
                        }
                    })
                    ->withCount('likes')
                    ->with('user')
                    ->projectCursorPaginate();
    }

    /**
     * @param int $groupId
     * @param int $postId
     * @return Model
     */
    public function findGroupPost(int $groupId, int $postId): Model
    {
        return $this->getModelClone()->newQuery()
                                    ->select($this->getSelect())
                                    ->with(array_merge($this->getWith(), ['medias']))
                                    ->where('group_id', $groupId)
                                    ->findOrFail($postId);
    }

    /**
     * @param int $postId
     * @param int $userId
     * @return bool
     */
    public function isAuthorThisPost(int $postId, int $userId): bool
    {
        return $this->getModelClone()->newQuery()
                                    ->where([
                                        ['post_id', '=', $postId],
                                        ['user_id', '=', $userId]
                                    ])
                                    ->exists();
    }

    /**
     * @param array $tags
     * @param string $sorting
     * @param int $perPage
     * @return ProjectCursorPaginator
     */
    public function getPostsWithTags(array $tags, string $sorting, int $perPage): ProjectCursorPaginator
    {
        $postsWithTags = $this->getModelClone()
            ->newQuery()
            ->select($this->getSelect())
            ->with([
                'user:user_id,username,first_name,last_name,avatar,verified,lastseen',
                'medias',
            ])
            ->whereHas('hashtags', function ($query) use ($tags) {
                $query->whereIn('id', $tags);
            })
            ->withCount('reactions', 'comments');

        if ($sorting == 'popular') {
            $postsWithTags->selectRaw('(
                (select count(*) from Wo_Reactions where Wo_Posts.post_id = Wo_Reactions.post_id) +
                (select count(*) from Wo_Comments where Wo_Posts.post_id = Wo_Comments.post_id) +
                (
                    select count(*) from Wo_Comment_Replies where Wo_Comment_Replies.comment_id
                        in (select id from Wo_Comments where Wo_Posts.post_id = Wo_Comments.post_id)
                ) +
                Wo_Posts.postShare
                ) as popularity')
                ->orderByDesc('popularity');
        }

        $postsWithTags->orderByDesc('time');

        return $postsWithTags->projectCursorPaginate($perPage);
    }

    /**
     * @return Collection
     */
    public function getHotTodayTagsPosts(): Collection
    {
        $dayBeforeTimestamp = now()->subDay()->getTimestamp();

        return $this->getModelClone()
            ->newQuery()
            ->select(['id', 'postText'])
            ->selectRaw('(
                (select count(*) from Wo_Reactions where Wo_Posts.post_id = Wo_Reactions.post_id) +
                (select count(*) from Wo_Comments where Wo_Posts.post_id = Wo_Comments.post_id)
                ) as popularity')
            ->where('postText', 'REGEXP', '#\[[0-9]+\]')
            ->where('time', '>=', $dayBeforeTimestamp)
            ->withCount('reactions', 'comments')
            ->orderByDesc('popularity')
            ->get();
    }

    /**
     * @param int $userId
     * @param array $ignorePostIds
     * @param int $perPage
     * @return ProjectCursorPaginator
     */
    public function getUserTimeline(int $userId, array $ignorePostIds, int $perPage): ProjectCursorPaginator
    {
        return $this->getModelClone()
            ->newQuery()
            ->select($this->getSelect())
            ->where(function ($query) use ($userId): void {
                $query->where('recipient_id', $userId)
                    ->orWhere(function ($subQuery) use ($userId): void {
                        $subQuery->where('user_id', $userId)
                            ->where('recipient_id', 0);
                    });
            })
            ->whereNotIn('post_id', $ignorePostIds)
            ->where('active', Post::POST_STATUS_ACTIVE)
            ->with($this->getWith())
            ->withCount($this->getWithCount())
            ->orderByDesc('time')
            ->projectCursorPaginate($perPage);
    }

    /**
     * @param int $userId
     * @param array $pinnedPostsIds
     * @param array $ignorePostIds
     * @param int|null $perPage
     * @return ProjectCursorPaginator
     */
    public function getUserTimelinePinned(int $userId, array $pinnedPostsIds = [], array $ignorePostIds = [], int $perPage = null): ProjectCursorPaginator
    {
        return $this->getModelClone()
            ->newQuery()
            ->select($this->getSelect())
            ->where(function ($query) use ($userId): void {
                $query->where('recipient_id', $userId)
                    ->orWhere(function ($subQuery) use ($userId): void {
                        $subQuery->where('user_id', $userId)
                            ->where('recipient_id', 0);
                    });
            })
            ->whereNotIn('post_id', $ignorePostIds)
            ->whereIn('post_id', $pinnedPostsIds)
            ->where('active', Post::POST_STATUS_ACTIVE)
            ->with($this->getWith())
            ->withCount($this->getWithCount())
            ->orderByDesc('time')
            ->projectCursorPaginate($perPage);
    }

    /**
     * @param int $userId
     * @param array $ignorePostIds
     * @param string $search
     * @param int $perPage
     * @return ProjectCursorPaginator
     */
    public function searchUserTimeline(int $userId, array $ignorePostIds, string $search, int $perPage): ProjectCursorPaginator
    {
        return $this->getModelClone()
            ->newQuery()
            ->select($this->getSelect())
            ->where(function ($query) use ($userId): void {
                $query->where('recipient_id', $userId)
                    ->orWhere(function ($subQuery) use ($userId): void {
                        $subQuery->where('user_id', $userId)
                            ->where('recipient_id', 0);
                    });
            })
            ->whereNotIn('post_id', $ignorePostIds)
            ->where('postText', 'like', "%{$search}%")
            ->where('active', Post::POST_STATUS_ACTIVE)
            ->with($this->getWith())
            ->withCount($this->getWithCount())
            ->orderByDesc('time')
            ->projectCursorPaginate($perPage);
    }

    /**
     * @param int $postId
     * @param int $userId
     * @return Post
     */
    public function findUserPost(int $postId, int $userId): Post
    {
        return $this->getModelClone()
            ->newQuery()
            ->where('user_id', $userId)
            ->findOrFail($postId);
    }
}
